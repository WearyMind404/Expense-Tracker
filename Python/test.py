from predict import predyears

predictions = predyears()
html = ""
for year, prediction in predictions:
    html += f"<h1>Year: {year}, Predicted Total Expenses: {prediction:.2f}</h1>"
print(html)
