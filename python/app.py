import os
from dotenv import load_dotenv
from pymongo import MongoClient

load_dotenv() # This loads the variables from your .env file

# Access them like this:
mongo_uri = os.getenv('MONGO_URI')
client = MongoClient(mongo_uri)