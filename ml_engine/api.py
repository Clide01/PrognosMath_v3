from flask import Flask, request, jsonify
import pickle
import numpy as np
import os
import re

app = Flask(__name__)

# Load the trained model safely
model_path = 'mathrise_model.pkl'
if os.path.exists(model_path):
    with open(model_path, 'rb') as f:
        model = pickle.load(f)
    print("PrognosMath ML Model loaded successfully.")
else:
    print("Warning: mathrise_model.pkl not found! Train the model first.")
    model = None

@app.route('/predict', methods=['POST'])
def predict():
    if model is None:
        return jsonify({'error': 'Model not loaded'}), 500

    data = request.get_json()
    
    # 5-Variable Robust Input
    prev_score = float(data.get('previous_score', 0))
    curr_score = float(data.get('current_score', 0))
    avg_time = float(data.get('avg_time_per_item', 0))
    scratch_usage = int(data.get('scratchpad_usage', 0))
    absences = int(data.get('absences', 0))
    
    # Format input array for the model
    features = np.array([[prev_score, curr_score, avg_time, scratch_usage, absences]])
    
    # Predict risk level (returns 'High', 'Moderate', or 'Low')
    risk_level = model.predict(features)[0]
    
    return jsonify({'risk_level': risk_level})


@app.route('/analyze-difficulty', methods=['POST'])
def analyze_difficulty():
    """
    Evaluates the difficulty of AI-generated questions based on NLP heuristics.
    Returns overall_difficulty (0.0 to 1.0) and expected_pass_rate (%).
    """
    data = request.get_json()
    questions = data.get('questions', [])

    if not questions:
        return jsonify({
            'overall_difficulty': 0.50,
            'expected_pass_rate': 75.0,
            'confidence': 0.50
        })

    total_word_count = 0
    complexity_score = 0
    
    # Keywords and symbols that indicate higher mathematical complexity
    complex_operators = [r'/', r'\*', r'\^', 'sqrt', 'fraction', 'decimal', 'equation', 'calculate', 'solve', 'probability', 'geometry']

    for q in questions:
        text = str(q.get('question_text', '')).lower()
        q_type = q.get('question_type', '')

        # 1. Lexical Feature: Word Count
        words = text.split()
        total_word_count += len(words)

        # 2. Mathematical Feature: Operator Complexity
        for op in complex_operators:
            if re.search(op, text):
                complexity_score += 0.15

        # 3. Format Feature: Question Type Weighting
        if q_type == 'problem_solving':
            complexity_score += 0.20
        elif q_type == 'computation':
            complexity_score += 0.10
        elif q_type == 'fill_in_the_blank':
            complexity_score += 0.05

    # Calculate Aggregates
    num_questions = len(questions)
    avg_word_count = total_word_count / num_questions

    # Base difficulty is 0.30. Add factors for reading length and math complexity.
    # Max difficulty capped at 0.95
    length_factor = min(avg_word_count, 60) / 150  # Max 0.40 from length
    math_factor = min(complexity_score, num_questions) / (num_questions * 1.5) # Max ~0.33 from complexity
    
    raw_difficulty = 0.30 + length_factor + math_factor
    overall_difficulty = min(round(raw_difficulty, 2), 0.95)

    # Expected Pass Rate is inversely correlated with difficulty
    # e.g., Difficulty 0.80 -> ~40% expected pass rate
    expected_pass_rate = round(100 - (overall_difficulty * 75), 2)

    return jsonify({
        'overall_difficulty': overall_difficulty,
        'expected_pass_rate': expected_pass_rate,
        'confidence': 0.88 # Static confidence metric until a neural network replaces this heuristic logic
    })

if __name__ == '__main__':
    app.run(host='127.0.0.1', port=5000, debug=True)