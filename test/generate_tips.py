from flask import Flask, request, jsonify
from transformers import pipeline

app = Flask(__name__)

# Load FinBERT model for financial text analysis
nlp = pipeline("text-classification", model="yiyanghkust/finbert-tone")

@app.route('/generate_tips', methods=['POST'])
def generate_tips():
    try:
        # Parse incoming data
        data = request.get_json()
        savings_type = data.get('savings_type')
        savings_data = data.get('savings_data', 0.0)

        # Generate financial tip prompt
        if savings_type == "individual":
            prompt = f"Provide financial advice for an individual who has saved ${savings_data}."
        elif savings_type == "group":
            prompt = f"Provide financial advice for a group with total savings of ${savings_data}."
        else:
            return jsonify({"error": "Invalid savings type."}), 400

        # Use FinBERT to analyze the prompt
        result = nlp(prompt)

        # Return the generated tip
        return jsonify({"tip": result[0]['label']})

    except Exception as e:
        return jsonify({"error": str(e)}), 500

if __name__ == '__main__':
    app.run(debug=True)
