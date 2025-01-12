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
def generate_tips():
    try:
        data = request.get_json()
        savings_type = data.get('savings_type')
        savings_data = float(data.get('savings_data', 0.0))
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
    # Generate a new prompt for detailed financial advice
    advice_prompt = f"Based on the sentiment '{sentiment}' with confidence {confidence:.2f}, provide detailed financial advice for the following context: {prompt}"
    
    # Get FinBERT prediction for advice
    advice_analysis = classifier(advice_prompt)
    advice_text = advice_analysis[0]['label']
    advice_confidence = advice_analysis[0]['score']

    # Simplify advice text
    strategy = f"Strategy: {advice_text}"
    risk_assessment = f"Risk: The sentiment suggests a {advice_text.lower()} risk level."
    recommendation = f"Recommendation: Based on the sentiment, consider actions that align with a {advice_text.lower()} outlook."

    return {
        "sentiment": sentiment,
        "confidence": confidence,
        "advice": {
            "strategy": strategy,
            "risk_assessment": risk_assessment,
            "recommendation": recommendation
        }
    }

if __name__ == '__main__':
    app.run(debug=True)