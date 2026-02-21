from flask import Flask, render_template, jsonify
from pymongo import MongoClient

app = Flask(__name__)

# Connect to your MongoDB
client = MongoClient('mongodb://localhost:27017/')
db = client['your_database_name']

# Route to serve your website
@app.route('/')
def home():
    return render_template('index.html')

# API endpoint for your JavaScript to call
@app.route('/api/data', methods=['GET'])
def get_data():
    items = list(db.collection_name.find({}, {'_id': 0}))
    return jsonify(items)

if __name__ == '__main__':
    app.run(debug=True)