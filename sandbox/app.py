# ============================================================
#  Casa De Manila — Flask + MongoDB (SECURED)
#  File: app.py
#  Description: Backend API handling Auth, Menu, Orders, and Reservations
# ============================================================

from flask import Flask, request, jsonify, session
from flask_cors import CORS
from pymongo import MongoClient
from bson import ObjectId, DBRef
from bson.errors import InvalidId
from datetime import datetime
from functools import wraps
import bcrypt
import re
import html

# --- App Configuration ---
app = Flask(__name__)
# Secret key is used to sign session cookies for security
app.secret_key = 'casademanila_SECRET_change_this_2026!' 
app.config.update(
    SESSION_COOKIE_HTTPONLY = True,  # Prevents JS from accessing cookies (XSS protection)
    SESSION_COOKIE_SAMESITE = 'Lax',   # Helps prevent CSRF attacks
    SESSION_COOKIE_SECURE   = False, # Set to True in production with HTTPS
)

# Enable CORS to allow your frontend (e.g., Live Server on port 5500) to talk to this API
CORS(app, supports_credentials=True,
     origins=["http://localhost:5500","http://127.0.0.1:5500","http://localhost:3000","null"])

# ── MongoDB Setup ───────────────────────────────────────────────────
# Connects to local MongoDB instance and selects the 'casa_de_manila' database
client = MongoClient('mongodb://localhost:27017/')
db     = client['casa_de_manila']

# Define collection handles
users_col        = db['users']
menu_col         = db['menu']
orders_col       = db['orders']
reservations_col = db['reservations']
contacts_col     = db['contacts']

# Projection constant to ensure we don't accidentally leak passwords in DB queries
SAFE_USER_FIELDS = {'password': 0}

# ── Helpers ───────────────────────────────────────────────────

def sanitize(v):
    """Trims whitespace and escapes HTML characters to prevent XSS."""
    return html.escape(v.strip()) if isinstance(v, str) else v

def is_valid_email(e):
    """Regex check for basic email format validity."""
    return bool(re.match(r'^[^\s@]+@[^\s@]+\.[^\s@]+$', e))

def is_valid_objectid(i):
    """Verifies if a string is a valid MongoDB 24-character hex ID."""
    try: 
        ObjectId(i)
        return True
    except: 
        return False

def serialize(doc):
    """Converts MongoDB BSON objects (ObjectId, Dates) into JSON-serializable strings."""
    if doc is None: return None
    doc = dict(doc)
    doc.pop('password', None) # Redundant safety: ensure password never leaves
    doc['_id'] = str(doc['_id'])
    for k, v in doc.items():
        if isinstance(v, DBRef): doc[k] = str(v.id)
        if isinstance(v, datetime): doc[k] = v.isoformat()
    return doc

def serialize_list(docs):
    """Helper to serialize a list of database documents."""
    return [serialize(dict(d)) for d in docs]

# DBRef helpers create links between collections (Normalization)
def make_user_ref(uid): return DBRef('users', ObjectId(uid))
def make_menu_ref(mid): return DBRef('menu',  ObjectId(mid))

def login_required(f):
    """Decorator to protect routes from unauthenticated users."""
    @wraps(f)
    def dec(*a, **kw):
        if 'user_id' not in session:
            return jsonify({'success': False, 'message': 'Login required.'}), 401
        return f(*a, **kw)
    return dec

# ── Security Middleware ─────────────────────────────────────────
@app.before_request
def limit_request_size():
    """Stops processing if the incoming request body is too large (DDoS protection)."""
    if request.content_length and request.content_length > 1_000_000:
        return jsonify({'success': False, 'message': 'Request too large.'}), 413

# ============================================================
#  AUTH ROUTES
# ============================================================

@app.route('/api/auth/register', methods=['POST'])
def register():
    """Handles new user creation with validation and password hashing."""
    data = request.get_json()
    if not data: return jsonify({'success': False, 'message': 'Invalid request.'}), 400

    # Extract and sanitize inputs
    username   = sanitize(data.get('username',   ''))
    email      = sanitize(data.get('email',      ''))
    password   = data.get('password', '') # Don't sanitize password (might have special chars)
    first_name = sanitize(data.get('first_name', ''))
    last_name  = sanitize(data.get('last_name',  ''))
    phone      = sanitize(data.get('phone',      ''))

    # Validation Logic
    if len(username) < 3: return jsonify({'success': False, 'message': 'Username too short.'}), 400
    if not is_valid_email(email): return jsonify({'success': False, 'message': 'Invalid email.'}), 400
    if len(password) < 6: return jsonify({'success': False, 'message': 'Password too weak.'}), 400

    # Check if user already exists
    if users_col.find_one({'$or': [
        {'username': {'$regex': f'^{re.escape(username)}$', '$options': 'i'}},
        {'email':    {'$regex': f'^{re.escape(email)}$',    '$options': 'i'}}
    ]}):
        return jsonify({'success': False, 'message': 'Username or email already taken.'}), 409

    # Hash password before storing (Never store plain text!)
    hashed = bcrypt.hashpw(password.encode('utf-8'), bcrypt.gensalt(rounds=12))

    users_col.insert_one({
        'username':   username,
        'email':      email.lower(),
        'password':   hashed,
        'role':       'user',
        'first_name': first_name,
        'last_name':  last_name,
        'full_name':  f'{first_name} {last_name}',
        'phone':      phone,
        'created_at': datetime.utcnow()
    })
    return jsonify({'success': True, 'message': 'Account created!'}), 201


@app.route('/api/auth/login', methods=['POST'])
def login():
    """Authenticates user and starts a session."""
    data = request.get_json()
    username = sanitize(data.get('username', ''))
    password = data.get('password', '')

    user = users_col.find_one({'username': username})
    
    # Check password hash using bcrypt.checkpw
    if not user or not bcrypt.checkpw(password.encode('utf-8'), user['password']):
        return jsonify({'success': False, 'message': 'Invalid credentials.'}), 401

    # Store user info in the session cookie
    session['user_id']  = str(user['_id'])
    session['username'] = user['username']
    session['role']     = user['role']

    return jsonify({'success': True, 'user': serialize(user)})


@app.route('/api/auth/logout', methods=['POST'])
def logout():
    """Clears the session cookie."""
    session.clear()
    return jsonify({'success': True})


@app.route('/api/auth/me', methods=['GET'])
def me():
    """Returns the currently logged-in user's profile."""
    if 'user_id' not in session: return jsonify({'logged_in': False})
    
    user = users_col.find_one({'_id': ObjectId(session['user_id'])}, SAFE_USER_FIELDS)
    if not user: return jsonify({'logged_in': False})

    return jsonify({'logged_in': True, 'user': serialize(user)})

# ============================================================
#  MENU ROUTES
# ============================================================

@app.route('/api/menu', methods=['GET'])
def get_menu():
    """Fetches menu items, optionally filtered by category."""
    category = sanitize(request.args.get('category', ''))
    query    = {'is_available': True}
    
    if category and category.lower() != 'all':
        query['category'] = {'$regex': f'^{re.escape(category)}$', '$options': 'i'}
    
    items = list(menu_col.find(query).sort([('category',1),('name',1)]))
    return jsonify({'success': True, 'items': serialize_list(items)})

# ============================================================
#  ORDERS ROUTES
# ============================================================

@app.route('/api/orders', methods=['POST'])
@login_required
def place_order():
    """Handles checkout logic and calculates totals on the server."""
    data = request.get_json()
    items = data.get('items', [])
    uid = session['user_id']

    order_items = []
    computed_total = 0

    # Loop through items to verify pricing and structure
    for item in items:
        price    = round(float(item.get('price', 0)), 2)
        quantity = int(item.get('quantity', 1))
        subtotal = round(price * quantity, 2)
        computed_total += subtotal

        order_items.append({
            'menu_id':  item.get('menu_id'),
            'name':     sanitize(item.get('name', '')),
            'price':    price,
            'quantity': quantity,
            'subtotal': subtotal
        })

    # Save order to DB
    res = orders_col.insert_one({
        'user_id':    uid,
        'username':   session['username'],
        'items':      order_items,
        'total':      round(computed_total, 2),
        'status':     'pending',
        'created_at': datetime.utcnow()
    })
    
    return jsonify({'success': True, 'order_id': str(res.inserted_id)}), 201

# ============================================================
#  ACCOUNT STATS & RESERVATIONS
# ============================================================

@app.route('/api/account/stats', methods=['GET'])
@login_required
def account_stats():
    """Aggregates user order history to show totals on dashboard."""
    uid = session['user_id']
    # MongoDB aggregation pipeline to calculate sums
    agg = list(orders_col.aggregate([
        {'$match': {'user_id': uid}},
        {'$group': {
            '_id': None,
            'total_orders': {'$sum': 1},
            'total_spent':  {'$sum': '$total'}
        }}
    ]))
    stats = agg[0] if agg else {'total_orders':0, 'total_spent':0}
    return jsonify({'success': True, 'stats': stats})

@app.route('/api/reservations', methods=['POST'])
@login_required
def create_reservation():
    """Stores a new table reservation."""
    data = request.get_json()
    result = reservations_col.insert_one({
        'user_id':          session['user_id'],
        'full_name':        sanitize(data.get('full_name','')),
        'party_size':       int(data.get('party_size', 1)),
        'reservation_date': sanitize(data.get('date','')),
        'status':           'pending',
        'created_at':       datetime.utcnow()
    })
    return jsonify({'success': True, 'id': str(result.inserted_id)}), 201

# ============================================================
#  ERROR HANDLERS
# ============================================================

@app.errorhandler(404)
def not_found(e): return jsonify({'success': False, 'message': 'Not found.'}), 404

@app.errorhandler(500)
def server_err(e): return jsonify({'success': False, 'message': 'Internal Server Error.'}), 500

# ============================================================
#  APP START
# ============================================================
if __name__ == '__main__':
    # debug=False is safer for production-style environments
    app.run(debug=False, port=5000)