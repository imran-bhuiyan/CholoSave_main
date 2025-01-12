from flask import Flask, request, jsonify
from flask_cors import CORS
import requests
import json

app = Flask(__name__)
CORS(app)

GEMINI_API_KEY = "AIzaSyBJeKaPqYT7Z-rJblgkbi9mqDNAEfGtWmw"
GEMINI_API_URL = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent"

def generate_financial_advice(prompt):
    try:
        payload = {
            "contents": [{
                "parts": [{
                    "text": f"""
                    Please provide a detailed financial analysis with exactly these three sections:
                    
                    Strategy:
                    Based on the financial data, provide clear strategic steps.
                    
                    Risk Assessment:
                    Evaluate potential risks and market conditions.
                    
                    Recommendations:
                    List specific actionable recommendations.
                    
                    Context:
                    {prompt}
                    """
                }]
            }]
        }

        headers = {
            'Content-Type': 'application/json'
        }

        response = requests.post(
            f"{GEMINI_API_URL}?key={GEMINI_API_KEY}",
            headers=headers,
            json=payload
        )

        print("API Response:", response.text)  # Debug log

        if response.status_code == 200:
            content = response.json()
            if 'candidates' in content and len(content['candidates']) > 0:
                text_response = content['candidates'][0]['content']['parts'][0]['text']
                
                # Parse sections more reliably
                sections = text_response.split('\n\n')
                strategy = ""
                risk = ""
                recommendation = ""
                
                for section in sections:
                    if 'Strategy:' in section:
                        strategy = section.replace('Strategy:', '').strip()
                    elif 'Risk Assessment:' in section:
                        risk = section.replace('Risk Assessment:', '').strip()
                    elif 'Recommendations:' in section:
                        recommendation = section.replace('Recommendations:', '').strip()

                return {
                    "sentiment": "analyzed",
                    "confidence": 1.0,
                    "advice": {
                        "strategy": strategy if strategy else "Strategy analysis pending.",
                        "risk_assessment": risk if risk else "Risk assessment pending.",
                        "recommendation": recommendation if recommendation else "Recommendations pending."
                    }
                }
            else:
                raise Exception("No valid response from Gemini API")
        else:
            raise Exception(f"API Error: {response.status_code}")

    except Exception as e:
        print(f"Error in generate_financial_advice: {str(e)}")
        return {
            "sentiment": "error",
            "confidence": 0,
            "advice": {
                "strategy": f"Error generating strategy: {str(e)}",
                "risk_assessment": "Risk assessment unavailable",
                "recommendation": "Recommendations unavailable"
            }
        }

if __name__ == '__main__':
    app.run(debug=True)