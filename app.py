from flask import Flask, render_template, request, redirect, url_for
from flask_sqlalchemy import SQLAlchemy
from datetime import datetime

app = Flask(__name__)

# Database Configuration (Ensure this matches your XAMPP MySQL credentials)
app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql+pymysql://root:@localhost/todo_master'
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = False
db = SQLAlchemy(app)

# Todo Model
class Todo(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(100), nullable=False)
    status = db.Column(db.String(50), nullable=False)
    created_at = db.Column(db.DateTime, default=datetime.utcnow)  # Auto-generated timestamp
    updated_at = db.Column(db.DateTime, onupdate=datetime.utcnow)

# Create tables if not exist
with app.app_context():
    db.create_all()

# Home Route - Show Todos
@app.route('/')
def index():
    todos = Todo.query.all()
    return render_template('index.html', todos=todos)

# Add Todo
@app.route('/add', methods=['POST'])
def add_todo():
    name = request.form.get('name')
    status = request.form.get('status')

    if name and status:
        new_todo = Todo(name=name, status=status)
        db.session.add(new_todo)
        db.session.commit()
        return redirect(url_for('index'))
    return "Error: Missing data"

# Update Todo
@app.route('/update/<int:id>', methods=['POST'])
def update_todo(id):
    todo = Todo.query.get(id)
    if todo:
        todo.status = request.form.get('status')
        db.session.commit()
        return redirect(url_for('index'))
    return "Error: Todo Not Found"

# Delete Todo
@app.route('/delete/<int:id>')
def delete_todo(id):
    todo = Todo.query.get(id)
    if todo:
        db.session.delete(todo)
        db.session.commit()
        return redirect(url_for('index'))
    return "Error: Todo Not Found"

if __name__ == "__main__":
    app.run(debug=True)
