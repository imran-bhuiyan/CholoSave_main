from flask import Flask, request, jsonify
from flask_cors import CORS
from transformers import AutoModelForSequenceClassification, AutoTokenizer
import torch

app = Flask(__name__)
CORS(app)

# Load FinBERT
MODEL = "ProsusAI/finbert"
tokenizer = AutoTokenizer.from_pretrained(MODEL)
model = AutoModelForSequenceClassification.from_pretrained(MODEL)

@app.route('/generate_tips', methods=['POST'])
def generate_tips():
    try:
        data = request.get_json()
        savings_type = data.get('savings_type')
        savings_data = float(data.get('savings_data', 0.0))

        # Generate analysis text
        analysis_text = f"Analysis for {savings_type} savings of ${savings_data}"
        
        # Get FinBERT prediction
        inputs = tokenizer(analysis_text, return_tensors="pt", padding=True)
        outputs = model(**inputs)
        sentiment = torch.nn.functional.softmax(outputs.logits, dim=-1)
        sentiment_score = sentiment.detach().numpy()[0]
        
        # Map sentiment scores to labels
        labels = ['negative', 'neutral', 'positive']
        sentiment_label = labels[sentiment_score.argmax()]
        confidence = float(sentiment_score.max())

        # Generate advice based on sentiment and amount
        advice = generate_advice(sentiment_label, savings_type, savings_data)

        response = {
            "sentiment": sentiment_label,
            "confidence": confidence,
            "advice": {
                "strategy": advice['strategy'],
                "risk_assessment": advice['risk'],
                "recommendation": advice['recommendation']
            }
        }

        return jsonify(response)

    except Exception as e:
        print(f"Error: {str(e)}")
        return jsonify({"error": str(e)}), 500

def generate_advice(sentiment, savings_type, amount):
    if sentiment == 'positive':
        strategy = "Capitalize on growth opportunities"
        risk = "Market conditions favor calculated risks"
    elif sentiment == 'neutral':
        strategy = "Maintain balanced investment approach"
        risk = "Consider moderate risk investments"
    else:
        strategy = "Focus on preservation and stability"
        risk = "Minimize risk exposure in current market"

    if savings_type == 'individual':
        if amount < 1000:
            recommendation = "Build emergency fund first, aim for stable savings growth"
        elif amount < 5000:
            recommendation = "Consider low-risk investment options while maintaining emergency fund"
        else:
            recommendation = "Explore diversified investment opportunities with professional guidance"
    else:
        if amount < 5000:
            recommendation = "Focus on group saving goals and regular contributions"
        elif amount < 20000:
            recommendation = "Consider professional group investment management"
        else:
            recommendation = "Explore advanced group investment strategies"

    return {
        "strategy": strategy,
        "risk": risk,
        "recommendation": recommendation
    }

if __name__ == '__main__':
    app.run(debug=True)