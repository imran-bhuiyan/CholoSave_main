from flask import Flask, request, jsonify
from flask_cors import CORS

app = Flask(__name__)
CORS(app)

@app.route('/generate_tips', methods=['POST'])
def generate_tips():
    try:
        data = request.get_json()
        savings_type = data.get('savings_type')
        savings_data = float(data.get('savings_data', 0.0))

        if savings_type == "individual":
            if savings_data < 1000:
                tip = "Start building an emergency fund by saving at least 10% of your monthly income. Aim for $1,000 initially."
            elif savings_data < 5000:
                tip = "Consider opening a high-yield savings account and start exploring low-risk investment options."
            else:
                tip = "Great savings! Look into diversifying your portfolio with index funds or consult a financial advisor."
        else:
            if savings_data < 5000:
                tip = "Encourage regular group contributions and consider setting group savings goals."
            elif savings_data < 20000:
                tip = "Your group has good momentum. Consider creating a group investment strategy."
            else:
                tip = "Impressive group savings! Consider consulting a financial advisor for group investment opportunities."

        return jsonify({"tip": tip})

    except Exception as e:
        return jsonify({"error": str(e)}), 500

if __name__ == '__main__':
    app.run(debug=True)