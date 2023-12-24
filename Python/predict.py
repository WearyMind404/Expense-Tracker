import pandas as pd
from sklearn.linear_model import LinearRegression
import pymysql 

def predyears():
    db = pymysql.connect(
        host='localhost',
        user='root',
        password='',
        database='expensetracker'
    )

# SQL query to retrieve data from the expenses table
    query = """
    SELECT expense_id, id, category_id, amount, expense_date
    FROM expenses
    """
    df = pd.read_sql_query(query, db)
    df['Date'] = pd.to_datetime(df['expense_date'], format='%m/%d/%Y')

# Extract the year from the 'Date' column
    df['Year'] = df['Date'].dt.year

# Group by 'Year' and sum the 'amount' for each year
    total_expenses_by_year = df.groupby('Year')['amount'].sum().reset_index()

# Rename the columns
    total_expenses_by_year.columns = ['year', 'totalexpenses']



# Create the linear regression model
    model = LinearRegression()

# Define the independent variable X (year)
    X = total_expenses_by_year[['year']]

# Define the dependent variable y (totalexpenses)
    y = total_expenses_by_year['totalexpenses']

# Fit the model
    model.fit(X, y)

# Create an array of the next 5 years
    years_to_predict = [2024, 2025, 2026, 2027, 2028]

# Predict the total expenses for each year and store the results in a list
    predictions = []
    for year in years_to_predict:
        year_data = pd.DataFrame({'year': [year]})
        prediction = model.predict(year_data)
        predictions.append((year, prediction[0]))
    return predictions