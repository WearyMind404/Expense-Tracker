
from flask import Flask
app = Flask(__name__)
from predict import predyears

@app.route('/')
def output():
    predictions = predyears()
    html = ""
    csv = ""
    for year, prediction in predictions:
        html += f"<p>Year: {year}, Predicted Total Expenses: {prediction:.2f}</p>"
        csv += f"{year},{prediction:.2f}\n"
 #python .\app.py  yo h1 xa ni change garesi python appy.py feri hanna parxa
    return csv



if __name__ == '__main__':
   app.run()
