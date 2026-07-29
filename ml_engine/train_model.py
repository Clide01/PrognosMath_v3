import pandas as pd
from sklearn.model_selection import train_test_split
from sklearn.ensemble import RandomForestClassifier
import pickle

print("Loading dataset...")
df = pd.read_csv('Math-Students.csv')

# Upgraded Feature Set (Make sure your CSV has these exact column names)
X = df[['previous_score', 'current_score', 'avg_time_per_item', 'scratchpad_usage', 'absences']]
y = df['risk_level']

X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

print("Training Random Forest Classifier model...")
model = RandomForestClassifier(n_estimators=100, random_state=42)
model.fit(X_train, y_train)

accuracy = model.score(X_test, y_test)
print(f"Model trained successfully! Accuracy: {accuracy * 100:.2f}%")

with open('mathrise_model.pkl', 'wb') as f:
    pickle.dump(model, f)

print("Model saved as 'mathrise_model.pkl'.")