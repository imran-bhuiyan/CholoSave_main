from flask import Flask, request, jsonify
from flask_cors import CORS
from transformers import AutoModelForSequenceClassification, AutoTokenizer, pipeline

app = Flask(__name__)
CORS(app)

# Load FinBERT
MODEL = "ProsusAI/finbert"
tokenizer = AutoTokenizer.from_pretrained(MODEL)
model = AutoModelForSequenceClassification.from_pretrained(MODEL)
classifier = pipeline("text-classification", model=model, tokenizer=tokenizer)

@app.route('/generate_tips', methods=['POST'])
@app.route('/generate_tips', methods=['POST'])
def generate_tips():
    try:
        data = request.get_json()
        savings_type = data.get('savings_type')
        savings_data = float(data.get('savings_data', 0.0))
        goal_amount = float(data.get('goal_amount', 0.0))
        emergency_fund = float(data.get('emergency_fund', 0.0))
        active_members = int(data.get('active_members', 0))
        remaining_weeks = int(data.get('remaining_weeks', 0))
        group_id = data.get('group_id')

        # Generate context-aware prompt
        if savings_type == 'group' and group_id and group_id != 'all':
            prompt = f"Analysis for group {group_id} savings of ${savings_data}"
        else:
            prompt = f"Analysis for {savings_type} savings of ${savings_data}"
        
        # Get FinBERT prediction
        analysis = classifier(prompt)
        sentiment_label = analysis[0]['label']
        confidence = analysis[0]['score']

        # Generate detailed financial advice based on FinBERT analysis
        response = generate_financial_advice(prompt, sentiment_label, confidence)

        return jsonify(response)

    except Exception as e:
        print(f"Error: {str(e)}")
        return jsonify({"error": str(e)}), 500

def generate_financial_advice(prompt, sentiment, confidence):
    # Calculate progress and needed metrics
    data = prompt.split('$')
    savings = float(data[1].split()[0].replace(',', '')) if len(data) > 1 else 0
    
    # Generate strategy based on sentiment
    if sentiment == 'positive':
        strategy = f"Current market conditions are favorable. With a confidence of {confidence:.0%}, we recommend capitalizing on growth opportunities while maintaining your current savings rate."
        risk = "Market indicators suggest a good environment for measured risk-taking. Consider diversifying investments while keeping a solid emergency fund."
        recommendation = "Take advantage of positive market conditions by exploring diversified investment options while maintaining regular savings contributions."
    elif sentiment == 'neutral':
        strategy = f"Market conditions are stable. With a confidence of {confidence:.0%}, we suggest maintaining a balanced approach between savings and conservative investments."
        risk = "Current market stability suggests maintaining your existing risk management strategy while staying alert to market changes."
        recommendation = "Continue your current savings pattern while building emergency funds. Review and rebalance your portfolio quarterly."
    else:  # negative
        strategy = f"Market conditions suggest caution. With a confidence of {confidence:.0%}, focus on preserving capital and maintaining emergency funds."
        risk = "Higher market volatility indicates the need for conservative positions and increased emergency fund allocation."
        recommendation = "Prioritize building emergency savings and consider more conservative investment options until market conditions improve."

    return {
        "sentiment": sentiment,
        "confidence": confidence,
        "advice": {
            "strategy": strategy,
            "risk_assessment": risk,
            "recommendation": recommendation
        }
    }

if __name__ == '__main__':
    app.run(debug=True)