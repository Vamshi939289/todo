from flask import Flask, render_template, request, redirect, url_for, flash
from flask_sqlalchemy import SQLAlchemy
from datetime import datetime

app = Flask(__name__)
app.secret_key = 'supersecretkey'  # Needed for flash messages

# Database Configuration (Update credentials if needed)
app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+pymysql://root:@localhost/todo_master'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False

# Initialize Database
db = SQLAlchemy(app)

# Todo Model
class Todo(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(100), nullable=False)
    status = db.Column(db.String(50), nullable=False, default="Pending")
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    updated_at = db.Column(db.DateTime, onupdate=datetime.utcnow)

# Create tables if they do not exist
with app.app_context():
    db.create_all()

# Home Route - Show Todos
@app.route('/')
def index():
    todos = Todo.query.order_by(Todo.created_at.desc()).all()
    return render_template('index.html', todos=todos)

# Add Todo
@app.route('/add', methods=['POST'])
def add_todo():
    name = request.form.get('name')
    if not name:
        flash("Error: Task name is required", "danger")
        return redirect(url_for('index'))

    new_todo = Todo(name=name, status="Pending")  # Ensure "Pending" is explicitly set
    db.session.add(new_todo)
    db.session.commit()
    
    flash("Task added successfully!", "success")
    return redirect(url_for('index'))

# Update Todo Status
@app.route('/update/<int:id>', methods=['POST'])
def update_todo(id):
    todo = Todo.query.get_or_404(id)
    todo.status = request.form.get('status', todo.status)
    todo.updated_at = datetime.utcnow()  # Ensure timestamp updates
    db.session.commit()
    
    flash("Task updated successfully!", "info")
    return redirect(url_for('index'))

# Delete Todo
@app.route('/delete/<int:id>', methods=['POST'])
def delete_todo(id):
    todo = Todo.query.get_or_404(id)
    db.session.delete(todo)
    db.session.commit()
    
    flash("Task deleted successfully!", "danger")
    return redirect(url_for('index'))

if __name__ == "__main__":
    app.run(debug=True)
