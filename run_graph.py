import pickle
import pandas as pd
import matplotlib.pyplot as plt
import seaborn as sns
import sys
import json
from sklearn.metrics import confusion_matrix, classification_report, accuracy_score

# Get the hospital type from the command line arguments
if len(sys.argv) != 2:
    print("Error: Hospital type is required (khusus or umum).")
    sys.exit(1)

hospital_type = sys.argv[1]

# Determine the model to load based on the hospital type
if hospital_type == 'khusus':
    model_file = 'model_rsk.pkl'
elif hospital_type == 'umum':
    model_file = 'model_rsu.pkl'
else:
    print("Error: Invalid hospital type. Use 'khusus' or 'umum'.")
    sys.exit(1)

# Load the model from the pickle file
with open(model_file, 'rb') as f:
    model_data = pickle.load(f)
    model = model_data['model']
    saved_accuracy = model_data['accuracy']
    saved_class_report = model_data['class_report']

# Load the CSV file
df = pd.read_csv('output.csv', delimiter=';')

# Define the feature columns
feature_columns = [
    'bed_occupation_rate (Persen)',
    'gross_death_rate (Persen)',
    'net_death_rate (Persen)',
    'bed_turn_over (Kali)',
    'turn_over_interval (Hari)',
    'average_length_of_stay (Hari)'
]

# Extract the feature data for prediction
input_data = df[feature_columns]

# Run predictions using the model
predicted_labels = model.predict(input_data)

# Use the "Prediction" column from the CSV as the actual labels
actual_labels = df['Prediction']

# Identify the unique classes present in the actual and predicted labels
unique_labels = sorted(set(actual_labels) | set(predicted_labels))

# Adjust the label names and target names based on the present classes
label_names_map = {0: 'Kurang Baik', 1: 'Baik', 2: 'Sangat Baik'}
present_label_names = [label_names_map[label] for label in unique_labels]

# Calculate the confusion matrix with the present labels
cm = confusion_matrix(actual_labels, predicted_labels, labels=unique_labels)

# Calculate the accuracy
accuracy = accuracy_score(actual_labels, predicted_labels)

# Generate the classification report with the present labels
classification_rep = classification_report(actual_labels, predicted_labels, target_names=present_label_names)

# Print accuracy and classification report
print(f"Accuracy from output.csv: {accuracy}")
print("Classification Report:")
print(classification_rep)

# Compare with saved accuracy from pickle file
print(f"Accuracy from saved model: {saved_accuracy}")

# Prepare text for rendering as image
report_text = f"Akurasi: {saved_accuracy:.2f}\n\nLaporan Klasifikasi:\n\n" + saved_class_report

# Create an image with the classification report
plt.figure(figsize=(10, 7))
plt.text(0.01, 0.05, report_text, {'fontsize': 12}, fontproperties='monospace')  # use a monospaced font
plt.axis('off')
plt.savefig('classification_report_output.png', bbox_inches='tight')  # Save the report as an image
plt.close()

# Plot the confusion matrix with custom labels
plt.figure(figsize=(10, 7))
sns.heatmap(cm, annot=True, fmt='d', cmap='Blues', xticklabels=present_label_names, yticklabels=present_label_names)
plt.xlabel('Predicted')
plt.ylabel('Actual')
plt.title('Confusion Matrix')
plt.savefig('confusion_matrix_output.png')  # Save the plot to a file
plt.close()
