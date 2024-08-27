import mysql.connector
import pandas as pd
import os
from sklearn.linear_model import LogisticRegression


hr_folder_path = 'c:/xampp/htdocs/HRMS/HR'
csv_filename = 'shortlisted_applicants.csv'
csv_file_path = os.path.join(hr_folder_path, csv_filename)

db = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="HRMS"
)

cursor = db.cursor()

query = "SELECT u.id, u.firstname, u.surname, u.experience FROM Users u JOIN Applicant a ON u.id = a.user_id"
cursor.execute(query)

# Convert the fetched data into a pandas DataFrame
data = pd.DataFrame(cursor.fetchall(), columns=['id', 'firstname', 'surname', 'experience'])

# Clean data: Convert experience to numeric and drop rows with missing values
data['experience'] = pd.to_numeric(data['experience'], errors='coerce')
data.dropna(subset=['experience'], inplace=True)

# a mock target variable based on experience >= 5 years
data['shortlisted'] = (data['experience'] >= 5).astype(int)

# Ensure there's enough data to train a model
if len(data) > 1:
    # Split data for training the model
    X = data[['experience']]
    y = data['shortlisted']
    
    # Create and train the model
    model = LogisticRegression()
    model.fit(X, y)

    # Predict whether an applicant should be shortlisted
    data['predicted_shortlisted'] = model.predict(X)
else:
    # If not enough data, assume all records are shortlisted
    data['predicted_shortlisted'] = data['shortlisted']

# Filter based on prediction
shortlisted = data[data['predicted_shortlisted'] == 1]

# Check if the CSV file exists
if os.path.exists(csv_file_path):
    try:
        # Load existing data
        existing_data = pd.read_csv(csv_file_path)
        
        # Check if the CSV is empty or has the correct columns
        if existing_data.empty or set(['id', 'firstname', 'surname', 'experience']) != set(existing_data.columns):
            updated_data = shortlisted
        else:
            # Combine with new data
            updated_data = pd.concat([existing_data, shortlisted], ignore_index=True)
    except pd.errors.EmptyDataError:
        # File is empty or has no valid data, use new data
        updated_data = shortlisted
else:
    # File does not exist, so use the new data
    updated_data = shortlisted

# Save the updated data to CSV
updated_data.to_csv(csv_file_path, index=False)

cursor.close()
db.close()
