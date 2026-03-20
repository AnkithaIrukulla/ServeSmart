import streamlit as st
import mysql.connector
import pandas as pd
from PIL import Image
from streamlit_option_menu import option_menu
from datetime import datetime

# ------------------ DB CONNECTION ------------------
conn = mysql.connector.connect(
    host='localhost',
    user='root',
    password='',
    database='servesmart'
)
cursor = conn.cursor(dictionary=True)

# ------------------ SESSION ------------------------
if 'logged_in' not in st.session_state:
    st.session_state.logged_in = False
if 'user_id' not in st.session_state:
    st.session_state.user_id = None
if 'role' not in st.session_state:
    st.session_state.role = None

# ------------------ UTILITY FUNCTIONS ----------------
def hash_password(password):
    import hashlib
    return hashlib.sha256(password.encode()).hexdigest()

def add_notification(user_id, message):
    cursor.execute("INSERT INTO notifications (user_id, message) VALUES (%s, %s)", (user_id, message))
    conn.commit()

def get_notifications(user_id):
    cursor.execute("SELECT * FROM notifications WHERE user_id=%s ORDER BY created_at DESC", (user_id,))
    return cursor.fetchall()

# ------------------ AUTH -----------------------------
def register():
    st.subheader("Register")
    name = st.text_input("Name")
    email = st.text_input("Email")
    password = st.text_input("Password", type="password")
    role = st.selectbox("Role", ["distributor", "ngo"])
    
    if st.button("Register"):
        hashed_pw = hash_password(password)
        try:
            cursor.execute("INSERT INTO users (name,email,password,role) VALUES (%s,%s,%s,%s)", (name,email,hashed_pw,role))
            conn.commit()
            st.success("Registered successfully! Login now.")
        except:
            st.error("Email already exists.")

def login():
    st.subheader("Login")
    email = st.text_input("Email")
    password = st.text_input("Password", type="password")
    if st.button("Login"):
        hashed_pw = hash_password(password)
        cursor.execute("SELECT * FROM users WHERE email=%s AND password=%s", (email, hashed_pw))
        user = cursor.fetchone()
        if user:
            st.session_state.logged_in = True
            st.session_state.user_id = user['id']
            st.session_state.role = user['role']
            st.success(f"Logged in as {user['name']}")
        else:
            st.error("Invalid credentials")

# ------------------ DASHBOARD ------------------------
def distributor_dashboard():
    menu = option_menu(None, ["Post Food","View/Edit Food","Notifications"], orientation="horizontal")
    
    # ---------- POST FOOD ----------
    if menu == "Post Food":
        st.subheader("Add New Food Item")
        name = st.text_input("Food Name")
        category = st.selectbox("Category", ["Fruits", "Vegetables", "Grains", "Dairy", "Other"])
        quantity = st.number_input("Quantity", min_value=1, step=1)
        price = st.number_input("Price", min_value=0.0, step=0.5)
        expiry = st.date_input("Expiry Date")
        image = st.file_uploader("Upload Image", type=["png","jpg","jpeg"])
        
        if st.button("Add Food"):
            img_path = None
            if image:
                img_path = f"uploads/{image.name}"
                with open(img_path,"wb") as f:
                    f.write(image.getbuffer())
            cursor.execute("INSERT INTO food_items (distributor_id,name,category,quantity,price,expiry_date,image) VALUES (%s,%s,%s,%s,%s,%s,%s)",
                           (st.session_state.user_id,name,category,quantity,price,expiry,img_path))
            conn.commit()
            st.success("Food added successfully")
            add_notification(st.session_state.user_id, f"New food posted: {name}")

    # ---------- VIEW/EDIT FOOD ----------
    elif menu == "View/Edit Food":
        st.subheader("Your Food Items")
        cursor.execute("SELECT * FROM food_items WHERE distributor_id=%s", (st.session_state.user_id,))
        foods = cursor.fetchall()
        for f in foods:
            col1, col2 = st.columns([1,2])
            with col1:
                if f['image']:
                    st.image(f['image'], width=100)
            with col2:
                st.write(f"**{f['name']}** | Category: {f['category']} | Qty: {f['quantity']} | Price: {f['price']} | Exp: {f['expiry_date']}")
                if st.button(f"Delete {f['id']}", key=f"del{f['id']}"):
                    cursor.execute("DELETE FROM food_items WHERE id=%s", (f['id'],))
                    conn.commit()
                    st.success("Deleted successfully")

    # ---------- NOTIFICATIONS ----------
    elif menu == "Notifications":
        st.subheader("Notifications")
        notifs = get_notifications(st.session_state.user_id)
        for n in notifs:
            st.write(f"[{n['status'].upper()}] {n['message']} ({n['created_at']})")

# ------------------ NGO DASHBOARD --------------------
def ngo_dashboard():
    menu = option_menu(None, ["Browse Food","Cart","Orders","Notifications"], orientation="horizontal")
    
    # ---------- BROWSE FOOD ----------
    if menu == "Browse Food":
        st.subheader("Browse Food Items")
        cursor.execute("SELECT * FROM food_items WHERE quantity>0")
        foods = pd.DataFrame(cursor.fetchall())
        
        # ---- FILTERS ----
        category_filter = st.multiselect("Category", options=foods['category'].unique())
        min_price, max_price = st.slider("Price Range", float(foods['price'].min()), float(foods['price'].max()), (0.0, float(foods['price'].max())))
        
        filtered = foods
        if category_filter:
            filtered = filtered[filtered['category'].isin(category_filter)]
        filtered = filtered[(filtered['price']>=min_price) & (filtered['price']<=max_price)]
        
        for idx, f in filtered.iterrows():
            col1, col2 = st.columns([1,2])
            with col1:
                if f['image']:
                    st.image(f['image'], width=100)
            with col2:
                st.write(f"**{f['name']}** | Category: {f['category']} | Qty: {f['quantity']} | Price: {f['price']} | Exp: {f['expiry_date']}")
                qty = st.number_input(f"Quantity to order {f['id']}", min_value=1, max_value=int(f['quantity']), key=f"qty{f['id']}")
                if st.button(f"Add to Cart {f['id']}", key=f"cart{f['id']}"):
                    cursor.execute("INSERT INTO orders (ngo_id, food_id, quantity) VALUES (%s,%s,%s)",
                                   (st.session_state.user_id, f['id'], qty))
                    conn.commit()
                    st.success("Added to cart")
                    add_notification(f['distributor_id'], f"NGO placed order for {f['name']}")

    # ---------- CART ----------
    elif menu == "Cart":
        st.subheader("Your Orders (Cart)")
        cursor.execute("""
        SELECT o.id, f.name, f.price, o.quantity, f.distributor_id 
        FROM orders o 
        JOIN food_items f ON o.food_id=f.id 
        WHERE o.ngo_id=%s AND o.status='pending'
        """, (st.session_state.user_id,))
        orders = cursor.fetchall()
        total = 0
        for o in orders:
            st.write(f"{o['name']} | Qty: {o['quantity']} | Price: {o['price']} | Total: {o['quantity']*o['price']}")
            total += o['quantity']*o['price']
            if st.button(f"Remove {o['id']}", key=f"rem{o['id']}"):
                cursor.execute("DELETE FROM orders WHERE id=%s", (o['id'],))
                conn.commit()
                st.experimental_rerun()
        st.write(f"**Grand Total: {total}**")
        if st.button("Place All Orders"):
            cursor.execute("UPDATE orders SET status='completed' WHERE ngo_id=%s AND status='pending'", (st.session_state.user_id,))
            conn.commit()
            st.success("Orders placed successfully")

    # ---------- NOTIFICATIONS ----------
    elif menu == "Notifications":
        st.subheader("Notifications")
        notifs = get_notifications(st.session_state.user_id)
        for n in notifs:
            st.write(f"[{n['status'].upper()}] {n['message']} ({n['created_at']})")

# ------------------ APP -----------------------------
st.title("ServeSmart Streamlit App")

if not st.session_state.logged_in:
    choice = st.radio("Go to", ["Login", "Register"])
    if choice=="Login":
        login()
    else:
        register()
else:
    st.write(f"Welcome, role: {st.session_state.role}")
    if st.session_state.role=="distributor":
        distributor_dashboard()
    else:
        ngo_dashboard()
    
    if st.button("Logout"):
        st.session_state.logged_in=False
        st.session_state.user_id=None
        st.session_state.role=None
        st.experimental_rerun()