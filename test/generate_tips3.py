from flask import Flask, request, jsonify
from flask_cors import CORS
from transformers import AutoModelForSequenceClassification, AutoTokenizer
import torch
import numpy as np

app = Flask(__name__)
CORS(app)

# Load FinBERT
MODEL = "ProsusAI/finbert"
tokenizer = AutoTokenizer.from_pretrained(MODEL)
model = AutoModelForSequenceClassification.from_pretrained(MODEL)

def analyze_risk(savings):
    if savings < 1000:
        return "low"
    elif savings < 5000:
        return "moderate"
    return "high"

@app.route('/generate_tips', methods=['POST'])
def generate_tips():
    try:
        data = request.get_json()
        savings_type = data.get('savings_type')
        savings_data = float(data.get('savings_data', 0.0))
        risk_level = analyze_risk(savings_data)

        # Structure the analysis context
        analysis_text = f"Analysis for {savings_type} savings of ${savings_data}"
        inputs = tokenizer(analysis_text, return_tensors="pt", padding=True)
        outputs = model(**inputs)
        sentiment = torch.nn.functional.softmax(outputs.logits, dim=-1)
        
        response = {
            "risk_level": risk_level,
            "sentiment_score": sentiment.detach().numpy().tolist(),
            "analysis": {
                "investment_strategy": get_investment_strategy(savings_data, risk_level),
                "risk_factors": get_risk_factors(savings_type, risk_level),
                "risk_management": get_risk_management(risk_level),
                "recommendations": get_recommendations(savings_type, savings_data)
            }
        }

        return jsonify(response)

    except Exception as e:
        return jsonify({"error": str(e)}), 500

def get_investment_strategy(savings, risk_level):
    strategies = {
        "low": ["High-yield savings accounts", "Certificates of deposit", "Treasury bills"],
        "moderate": ["Index funds", "Blue-chip stocks", "Government bonds"],
        "high": ["Diversified portfolio", "Growth stocks", "Real estate investment trusts"]
    }
    return strategies.get(risk_level, ["Conservative savings approach"])

def get_risk_factors(savings_type, risk_level):
    factors = {
        "individual": {
            "low": ["Limited emergency fund", "Cash flow vulnerability"],
            "moderate": ["Market volatility", "Inflation risk"],
            "high": ["Market downturns", "Sector concentration risk"]
        },
        "group": {
            "low": ["Collective risk", "Withdrawal pressure"],
            "moderate": ["Group decision making", "Market timing"],
            "high": ["Investment consensus", "Portfolio management"]
        }
    }
    return factors.get(savings_type, {}).get(risk_level, ["Default risk factors"])

def get_risk_management(risk_level):
    management = {
        "low": ["Build emergency fund", "Regular savings plan"],
        "moderate": ["Diversification", "Dollar-cost averaging"],
        "high": ["Portfolio rebalancing", "Professional consultation"]
    }
    return management.get(risk_level, ["Conservative management approach"])

def get_recommendations(savings_type, amount):
    if savings_type == "individual":
        if amount < 1000:
            return "Focus on building emergency savings and establishing regular saving habits."
        elif amount < 5000:
            return "Consider diversifying into low-risk investment options while maintaining emergency fund."
        else:
            return "Explore diversified investment portfolio with professional guidance."
    else:
        if amount < 5000:
            return "Establish group saving goals and contribution structure."
        elif amount < 20000:
            return "Consider professional group investment management and diversification."
        else:
            return "Explore advanced investment strategies with professional advisory."

if __name__ == '__main__':
    app.run(debug=True)