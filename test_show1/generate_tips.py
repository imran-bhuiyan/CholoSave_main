from flask import Flask, request, jsonify
from flask_cors import CORS
from transformers import AutoModelForSequenceClassification, AutoTokenizer, pipeline
import numpy as np
from datetime import datetime, timedelta

app = Flask(__name__)
CORS(app)

# Initialize FinBERT
MODEL = "ProsusAI/finbert"
tokenizer = AutoTokenizer.from_pretrained(MODEL)
model = AutoModelForSequenceClassification.from_pretrained(MODEL)
classifier = pipeline("text-classification", model=model, tokenizer=tokenizer)

@app.route('/generate_tips', methods=['POST'])
def generate_tips():
    try:
        data = request.get_json()
        savings_type = data.get('savings_type')
        financial_data = data.get('financial_data', {})
        
        if savings_type == 'individual':
            return jsonify(analyze_individual(financial_data))
        elif savings_type == 'group':
            group_id = data.get('group_id')
            return jsonify(analyze_group(financial_data, group_id))
        
        return jsonify({"error": "Invalid analysis type"}), 400
        
    except Exception as e:
        print(f"Error: {str(e)}")
        return jsonify({"error": str(e)}), 500

def analyze_individual(data):
    individual_data = data.get('individual', {})
    contributions = data.get('contributions', [])
    
    total_savings = float(individual_data.get('total_savings', 0))
    total_invested = float(individual_data.get('total_invested_amount', 0))
    total_loans = int(individual_data.get('total_loans', 0))
    completed_loans = int(individual_data.get('completed_loans', 0))
    
    tips = []
    
    # Investment analysis
    investment_ratio = (total_invested / total_savings * 100) if total_savings > 0 else 0
    if investment_ratio < 20:
        tips.append({
            "category": "Investment Strategy",
            "tip": "Consider diversifying your savings by investing more through your groups. A balanced portfolio typically has 20-30% in investments.",
            "priority": "High"
        })
    elif investment_ratio > 60:
        tips.append({
            "category": "Risk Management",
            "tip": "Your investment ratio is quite high. Consider maintaining more liquid savings for emergencies.",
            "priority": "Medium"
        })
    
    # Loan management
    if total_loans > 0:
        completion_rate = (completed_loans / total_loans * 100)
        if completion_rate < 80:
            tips.append({
                "category": "Loan Management",
                "tip": f"Your loan completion rate is {completion_rate:.1f}%. Focus on repaying existing loans before taking new ones.",
                "priority": "High"
            })
    
    # Savings distribution
    if len(contributions) > 0:
        max_contribution = max(float(g['contribution']) for g in contributions)
        min_contribution = min(float(g['contribution']) for g in contributions)
        if max_contribution > min_contribution * 3:
            tips.append({
                "category": "Savings Balance",
                "tip": "Your group contributions are significantly uneven. Consider balancing your savings across groups for better risk management.",
                "priority": "Medium"
            })
    
    # Savings growth analysis
    if total_savings < 5000:
        tips.append({
            "category": "Emergency Fund",
            "tip": "Work on building an emergency fund of at least $5,000. Set up automatic weekly savings transfers.",
            "priority": "High"
        })
    
    return {
        "analysis": {
            "savings_total": f"${total_savings:,.2f}",
            "investment_ratio": f"{investment_ratio:.1f}%",
            "loan_completion": f"{(completed_loans/total_loans*100 if total_loans > 0 else 100):.1f}%",
            "active_groups": len(contributions)
        },
        "tips": tips
    }

def analyze_group(data, group_id):
    groups = data.get('groups', [])
    
    if group_id == 'all':
        return analyze_all_groups(groups)
    
    # Find specific group data
    group_data = next((g for g in groups if str(g['group_id']) == str(group_id)), None)
    if not group_data:
        return {"error": "Group not found"}, 404
    
    tips = []
    
    # Calculate key metrics
    total_savings = float(group_data['total_group_savings'])
    goal_amount = float(group_data['goal_amount'])
    emergency_fund = float(group_data['emergency_fund'])
    total_investments = float(group_data['total_investments'])
    active_members = int(group_data['active_members'])
    days_active = int(group_data['days_active'])
    
    # Goal progress analysis
    progress_percentage = (total_savings / goal_amount * 100) if goal_amount > 0 else 0
    if progress_percentage < 40:
        weekly_target = (goal_amount - total_savings) / (active_members * 52)
        tips.append({
            "category": "Savings Goal",
            "tip": f"To reach your goal, each member should aim to save ${weekly_target:.2f} weekly. Consider increasing regular contributions.",
            "priority": "High"
        })
    
    # Emergency fund analysis
    recommended_emergency = total_savings * 0.15
    if emergency_fund < recommended_emergency:
        tips.append({
            "category": "Emergency Fund",
            "tip": f"Your emergency fund is below the recommended 15% of total savings. Consider increasing it by ${(recommended_emergency - emergency_fund):.2f}.",
            "priority": "High"
        })
    
    # Investment analysis
    investment_ratio = (total_investments / total_savings * 100) if total_savings > 0 else 0
    if investment_ratio < 20:
        tips.append({
            "category": "Investment Opportunity",
            "tip": "Consider increasing group investments to 20-30% of total savings for better long-term growth.",
            "priority": "Medium"
        })
    
    # Member contribution analysis
    avg_per_member = total_savings / active_members if active_members > 0 else 0
    avg_daily_saving = total_savings / days_active if days_active > 0 else 0
    
    # Group dynamics tips
    if active_members < 5:
        tips.append({
            "category": "Group Growth",
            "tip": "Consider recruiting more members to increase the group's saving potential and spread risk.",
            "priority": "Medium"
        })
    
    return {
        "analysis": {
            "goal_progress": f"{progress_percentage:.1f}%",
            "savings_per_member": f"${avg_per_member:.2f}",
            "daily_growth": f"${avg_daily_saving:.2f}",
            "investment_ratio": f"{investment_ratio:.1f}%",
            "emergency_ratio": f"{(emergency_fund/total_savings*100 if total_savings > 0 else 0):.1f}%"
        },
        "tips": tips
    }

def analyze_all_groups(groups):
    total_savings = sum(float(g['total_group_savings']) for g in groups)
    total_investments = sum(float(g['total_investments']) for g in groups)
    total_members = sum(int(g['active_members']) for g in groups)
    total_emergency = sum(float(g['emergency_fund']) for g in groups)
    
    tips = []
    
    # Overall portfolio analysis
    investment_ratio = (total_investments / total_savings * 100) if total_savings > 0 else 0
    if investment_ratio < 15:
        tips.append({
            "category": "Portfolio Growth",
            "tip": "Consider increasing investments across groups to improve long-term returns.",
            "priority": "High"
        })
    
    # Emergency fund distribution
    emergency_ratio = (total_emergency / total_savings * 100) if total_savings > 0 else 0
    if emergency_ratio < 10:
        tips.append({
            "category": "Risk Management",
            "tip": "Total emergency funds across groups are low. Consider building stronger safety nets.",
            "priority": "High"
        })
    
    # Group performance comparison
    savings_variance = np.var([float(g['total_group_savings']) for g in groups])
    if savings_variance > (total_savings / len(groups)) ** 2 * 0.5:
        tips.append({
            "category": "Group Balance",
            "tip": "There's significant variation in group performance. Consider standardizing saving practices across groups.",
            "priority": "Medium"
        })
    
    return {
        "analysis": {
            "total_savings": f"${total_savings:,.2f}",
            "total_investments": f"${total_investments:,.2f}",
            "total_members": total_members,
            "average_per_group": f"${(total_savings/len(groups) if len(groups) > 0 else 0):,.2f}",
            "emergency_ratio": f"{emergency_ratio:.1f}%"
        },
        "tips": tips
    }

if __name__ == '__main__':
    app.run(debug=True)