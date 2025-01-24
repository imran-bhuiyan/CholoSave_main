from flask import Flask, request, jsonify
from flask_cors import CORS
from groq import Groq
import os

app = Flask(__name__)
CORS(app)

# Initialize Groq client
client = Groq(
    api_key=os.environ.get("GROQ_API_KEY"),
)

def generate_financial_advice(savings_data, savings_type, question):
    try:
        prompt = f"""
        You are a financial assistant specializing in actionable advice. Your task is to provide clear, detailed, and personalized financial recommendations.

        User Financial Profile:
        - Total savings: ${float(savings_data['individual_savings']):,.2f}
        - Monthly income: ${float(savings_data['monthly_income']):,.2f}
        - Monthly expenses: ${float(savings_data['monthly_expenses']):,.2f}
        - Group contributions:
        {chr(10).join([f"  - {group['group_name']}: ${float(group['total_contribution']):,.2f} saved out of ${float(group['goal_amount']):,.2f}" for group in savings_data['group_contributions']]) or 'No group contributions available.'}
        - Investments:
        {chr(10).join([f"  - {inv['type']}: ${float(inv['amount']):,.2f} invested, ${float(inv['returns']):,.2f} returns, ROI: {float(inv['roi'])}%" for inv in savings_data['investments']]) or 'No investments available.'}
        - Loan status: {savings_data['loan_status']}
        - Emergency fund: ${float(savings_data['emergency_fund']):,.2f} out of ${float(savings_data['emergency_fund_goal']):,.2f} goal.

        User’s Question:
        "{question}"

        Provide:
        1. A concise summary of their financial situation.
        2. A specific, step-by-step strategy to achieve their financial goal.
        3. Actionable tips to improve savings, manage investments, or reduce expenses.
        4. Highlight any risks or additional considerations the user should be aware of.

        Respond in a friendly, professional tone, using bullet points or numbered lists for clarity.
        """

        completion = client.chat.completions.create(
            model="llama-3.3-70b-versatile",
            messages=[
                {
                    "role": "system",
                    "content": "You are a professional financial advisor providing actionable advice."
                },
                {
                    "role": "user",
                    "content": prompt
                }
            ],
            temperature=0.7,
            max_tokens=1024
        )

        response = completion.choices[0].message.content

        # Parse the response into structured format
        advice = {
            'title': 'Financial Advice',
            'main_advice': response.split('\n')[0],
            'steps': [step.strip() for step in response.split('\n')[1:] if step.strip()]
        }

        return advice

    except Exception as e:
        print(f"Error generating advice: {e}")
        return None

@app.route('/generate_tips', methods=['POST'])
def generate_tips():
    try:
        data = request.get_json()
        savings_data = data.get('savings_data', {})
        savings_type = data.get('savings_type')
        question = data.get('question')

        # Ensure all necessary fields are present
        required_fields = ['individual_savings', 'monthly_income', 'monthly_expenses', 'group_contributions', 'investments', 'loan_status', 'emergency_fund', 'emergency_fund_goal']
        for field in required_fields:
            if field not in savings_data:
                return jsonify({
                    'status': 'error',
                    'error': f"Missing field in savings_data: {field}"
                }), 400

        advice = generate_financial_advice(savings_data, savings_type, question)

        if advice:
            return jsonify({
                'status': 'success',
                'advice': advice
            })
        else:
            return jsonify({
                'status': 'error',
                'error': 'Failed to generate advice'
            }), 500

    except Exception as e:
        return jsonify({
            'status': 'error',
            'error': str(e)
        }), 500

if __name__ == '__main__':
    app.run(debug=True)
