import pickle
import sys
import json
import numpy as np
import pandas as pd
import matplotlib.pyplot as plt
import os
from sklearn.metrics import accuracy_score, classification_report, confusion_matrix
import seaborn as sns

# Check if the model type is provided as an argument
if len(sys.argv) < 3:
    print("Error: Please provide the model type (rsk or rsu) and input data.")
    sys.exit(1)

# Get the model type and input data
model_type = sys.argv[1]
input_data = json.loads(sys.argv[2])

# Determine which model to load based on the model type argument
if model_type == 'rsk':
    model_file = 'model_rsk.pkl'
elif model_type == 'rsu':
    model_file = 'model_rsu.pkl'
else:
    print("Error: Invalid model type. Use 'rsk' or 'rsu'.")
    sys.exit(1)

# Check if the model file exists
if not os.path.exists(model_file):
    print(f"Error: Model file {model_file} does not exist.")
    sys.exit(1)

# Load the selected model
with open(model_file, 'rb') as f:
    model_data = pickle.load(f)

model = model_data['model']

# Define the feature names (assuming they are the same for both models)
feature_names = [
    'bed_occupation_rate (Persen)', 
    'gross_death_rate (Persen)', 
    'net_death_rate (Persen)', 
    'bed_turn_over (Kali)', 
    'turn_over_interval (Hari)', 
    'average_length_of_stay (Hari)'
]

# Convert input data to a DataFrame with the correct feature names
input_df = pd.DataFrame(input_data, columns=feature_names)

# Run prediction
predictions = model.predict(input_df)

# Assuming you have true labels to compare (for example purposes, using the predicted labels as true labels)
true_labels = predictions  # Replace this with actual true labels if available

# Calculate accuracy
accuracy = accuracy_score(true_labels, predictions)

# Generate classification report
classification_rep = classification_report(true_labels, predictions, output_dict=True)

# Generate confusion matrix
conf_matrix = confusion_matrix(true_labels, predictions)

# Plot confusion matrix and save it as an image
plt.figure(figsize=(8, 6))
sns.heatmap(conf_matrix, annot=True, fmt='d', cmap='Blues', xticklabels=['Kurang Baik', 'Baik', 'Sangat Baik'], yticklabels=['Kurang Baik', 'Baik', 'Sangat Baik'])
plt.xlabel('Predicted')
plt.ylabel('Actual')
plt.title('Confusion Matrix')
plt.savefig('confusion_matrix.png')
plt.close()

# Save the classification report as text (optional)
with open('classification_report.txt', 'w') as f:
    f.write(f"Accuracy: {accuracy}\n")
    f.write(json.dumps(classification_rep, indent=2))

# Generate and save a plot (for example, a scatter plot)
plt.figure(figsize=(8, 6))
plt.scatter(input_df['bed_occupation_rate (Persen)'], input_df['gross_death_rate (Persen)'], c=predictions, cmap='viridis')
plt.xlabel('Bed Occupation Rate (Persen)')
plt.ylabel('Gross Death Rate (Persen)')
plt.title('Prediction Results')
plt.colorbar(label='Cluster')
plt.savefig('prediction_plot.png')
plt.close()

# Return all results as JSON
output_json = {
    "predictions": predictions.tolist(),
    "accuracy": accuracy,
    "classification_report": classification_rep,
    "confusion_matrix": conf_matrix.tolist(),
    "confusion_matrix_image": "confusion_matrix.png",
    "prediction_plot_image": "prediction_plot.png"
}

print(json.dumps(output_json))
