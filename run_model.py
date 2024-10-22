import pickle
import sys
import json
import numpy as np
import pandas as pd
import matplotlib.pyplot as plt
import os

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

# Return the predictions as JSON
print(json.dumps(predictions.tolist()))

# Generate and save a plot (for example, a scatter plot)
plt.figure(figsize=(8, 6))
plt.scatter(input_df['bed_occupation_rate (Persen)'], input_df['gross_death_rate (Persen)'], c=predictions, cmap='viridis')
plt.xlabel('Bed Occupation Rate (Persen)')
plt.ylabel('Gross Death Rate (Persen)')
plt.title('Prediction Results')
plt.colorbar(label='Cluster')
plt.savefig('plot.png')  # Save the plot to a file
