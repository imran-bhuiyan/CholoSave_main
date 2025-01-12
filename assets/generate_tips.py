from flask import Flask, request, jsonify
from flask_cors import CORS
import numpy as np
from datetime import datetime, timedelta

app = Flask(__name__)
CORS(app)

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
    active_loans = int(individual_data.get('active_loans', 0))
    
    tips = []
    
    # Investment analysis with corrected calculations
    investment_ratio = min((total_invested / max(total_savings, 1) * 100), 100)
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
    
    # Loan management with active loans consideration
    if active_loans > 0:
        tips.append({
            "category": "Active Loans",
            "tip": f"You have {active_loans} active loan(s). Focus on timely repayments to maintain good credit within your groups.",
            "priority": "High"
        })
    
    if total_loans > 0:
        completion_rate = (completed_loans / total_loans * 100)
        if completion_rate < 80:
            tips.append({
                "category": "Loan Management",
                "tip": f"Your loan completion rate is {completion_rate:.1f}%. Focus on repaying existing loans before taking new ones.",
                "priority": "High"
            })
    
    # Savings distribution analysis
    if len(contributions) > 0:
        contributions_list = [float(g['contribution']) for g in contributions]
        max_contribution = max(contributions_list)
        min_contribution = min(contributions_list)
        avg_contribution = sum(contributions_list) / len(contributions_list)
        
        if max_contribution > min_contribution * 3 and min_contribution > 0:
            tips.append({
                "category": "Savings Balance",
                "tip": "Your group contributions are significantly uneven. Consider balancing your savings across groups for better risk management.",
                "priority": "Medium"
            })
    
    # Emergency fund analysis
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
            "loan_completion": f"{(completed_loans/max(total_loans, 1)*100):.1f}%",
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
    
    # Calculate key metrics with proper validation
    total_savings = max(float(group_data['total_group_savings']), 0)
    goal_amount = max(float(group_data['goal_amount']), 1)  # Prevent division by zero
    emergency_fund = float(group_data['emergency_fund'])
    total_investments = float(group_data['total_investments'])
    active_members = max(int(group_data['active_members']), 1)  # Prevent division by zero
    days_active = max(int(group_data['days_active']), 1)  # Prevent division by zero
    
    # Goal progress analysis
    progress_percentage = min((total_savings / goal_amount * 100), 100)
    if progress_percentage < 40:
        weekly_target = (goal_amount - total_savings) / (active_members * 52)
        tips.append({
            "category": "Savings Goal",
            "tip": f"To reach your goal, each member should aim to save ${weekly_target:.2f} weekly. Consider increasing regular contributions.",
            "priority": "High"
        })
    
    # Emergency fund analysis
    # Emergency fund analysis
    recommended_emergency = total_savings * 0.15
    if emergency_fund < recommended_emergency:
        tips.append({
            "category": "Emergency Fund",
            "tip": f"Your emergency fund is below the recommended 15% of total savings. Consider increasing it by ${(recommended_emergency - emergency_fund):.2f}.",
            "priority": "High"
        })
    
    # Investment analysis with capped ratio
    investment_ratio = min((total_investments / max(total_savings, 1) * 100), 100)
    if investment_ratio < 20:
        tips.append({
            "category": "Investment Opportunity",
            "tip": "Consider increasing group investments to 20-30% of total savings for better long-term growth.",
            "priority": "Medium"
        })
    
    # Member contribution analysis
    avg_per_member = total_savings / active_members
    # Calculate daily growth based on recent period (last 30 days) instead of all-time
    avg_daily_saving = total_savings / min(days_active, 30) if days_active > 0 else 0
    
    # Group dynamics tips
    if active_members < 5:
        tips.append({
            "category": "Group Growth",
            "tip": "Consider recruiting more members to increase the group's saving potential and spread risk.",
            "priority": "Medium"
        })
    
    # Add tip for high daily growth
    if avg_daily_saving > 1000:  # Arbitrary threshold, adjust based on your needs
        tips.append({
            "category": "Growth Management",
            "tip": "Your group shows strong growth. Consider diversifying into different investment types to maximize returns.",
            "priority": "Medium"
        })
    
    return {
        "analysis": {
            "goal_progress": f"{progress_percentage:.1f}%",
            "savings_per_member": f"${avg_per_member:.2f}",
            "daily_growth": f"${avg_daily_saving:.2f}",
            "investment_ratio": f"{investment_ratio:.1f}%",
            "emergency_ratio": f"{(emergency_fund/max(total_savings, 1)*100):.1f}%"
        },
        "tips": tips
    }

def analyze_all_groups(groups):
    # Initialize aggregated metrics
    total_savings = 0
    total_investments = 0
    total_members = 0
    total_emergency = 0
    all_savings = []
    
    # Calculate aggregated metrics with proper validation
    for group in groups:
        group_savings = float(group.get('total_group_savings', 0))
        total_savings += max(group_savings, 0)
        total_investments += max(float(group.get('total_investments', 0)), 0)
        total_members += max(int(group.get('active_members', 0)), 0)
        total_emergency += max(float(group.get('emergency_fund', 0)), 0)
        if group_savings > 0:
            all_savings.append(group_savings)
    
    tips = []
    
    # Overall portfolio analysis with capped ratio
    investment_ratio = min((total_investments / max(total_savings, 1) * 100), 100)
    if investment_ratio < 15:
        tips.append({
            "category": "Portfolio Growth",
            "tip": "Consider increasing investments across groups to improve long-term returns.",
            "priority": "High"
        })
    
    # Emergency fund distribution
    emergency_ratio = (total_emergency / max(total_savings, 1) * 100)
    if emergency_ratio < 10:
        tips.append({
            "category": "Risk Management",
            "tip": "Total emergency funds across groups are low. Consider building stronger safety nets.",
            "priority": "High"
        })
    
    # Group performance comparison
    if len(all_savings) > 1:
        savings_variance = np.var(all_savings)
        mean_savings = np.mean(all_savings)
        coefficient_of_variation = (np.sqrt(savings_variance) / mean_savings * 100) if mean_savings > 0 else 0
        
        if coefficient_of_variation > 50:  # High variance threshold
            tips.append({
                "category": "Group Balance",
                "tip": "There's significant variation in group performance. Consider standardizing saving practices across groups.",
                "priority": "Medium"
            })
    
    # Member distribution analysis
    if len(groups) > 0:
        avg_members = total_members / len(groups)
        if avg_members < 5:
            tips.append({
                "category": "Membership Growth",
                "tip": "Your groups have relatively few members on average. Consider recruiting more members to increase collective saving power.",
                "priority": "Medium"
            })
    
    return {
        "analysis": {
            "total_savings": f"${total_savings:,.2f}",
            "total_investments": f"${total_investments:,.2f}",
            "total_members": total_members,
            "average_per_group": f"${(total_savings/max(len(groups), 1)):,.2f}",
            "emergency_ratio": f"{emergency_ratio:.1f}%"
        },
        "tips": tips
    }

if __name__ == '__main__':
    app.run(debug=True)