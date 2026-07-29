import csv
import random

# The 5 variables we added to train_model.py, plus the target risk_level
headers = ['previous_score', 'current_score', 'avg_time_per_item', 'scratchpad_usage', 'absences', 'risk_level']

print("Generating smart dummy data for PrognosMath...")

with open('Math-Students.csv', 'w', newline='') as file:
    writer = csv.writer(file)
    writer.writerow(headers)

    # Generate 300 realistic student records
    for _ in range(300):
        category = random.random()
        
        if category < 0.33:
            # HIGH RISK: Low scores, high absences, guessing (too fast) or struggling (too slow), empty scratchpad
            prev = random.randint(30, 65)
            curr = random.randint(20, 59)
            time = random.choice([random.randint(5, 15), random.randint(100, 180)]) 
            scratch = random.randint(0, 1)
            absences = random.randint(4, 10)
            risk = 'High'
            
        elif category < 0.66:
            # MODERATE RISK: Average scores, average time, some scratchpad usage
            prev = random.randint(60, 85)
            curr = random.randint(60, 74)
            time = random.randint(20, 90)
            scratch = random.randint(1, 3)
            absences = random.randint(2, 5)
            risk = 'Moderate'
            
        else:
            # LOW RISK (ON TRACK): High scores, optimal time, heavy scratchpad usage, low absences
            prev = random.randint(75, 100)
            curr = random.randint(80, 100)
            time = random.randint(30, 70)
            scratch = random.randint(3, 5)
            absences = random.randint(0, 2)
            risk = 'Low'

        writer.writerow([prev, curr, time, scratch, absences, risk])

print("Success! Math-Students.csv has been created with 300 records.")