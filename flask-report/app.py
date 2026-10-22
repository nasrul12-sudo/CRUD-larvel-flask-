from flask import Flask, render_template, request
import mysql.connector
import os

app = Flask(__name__)

# Ambil config dari environment variable, fallback ke default
DB_CONFIG = {
    'host':     os.getenv('DB_HOST', '127.0.0.1'),
    'user':     os.getenv('DB_USER', 'root'),
    'password': os.getenv('DB_PASS', 'nasrul'),
    'database': os.getenv('DB_NAME', 'database_erp'),
}

def get_db():
    return mysql.connector.connect(**DB_CONFIG)

@app.route('/')
def index():
    return '<a href="/weight-diff">Lihat Selisih Berat per Produk</a>'

@app.route('/weight-diff')
def weight_diff():
    conn = get_db()
    cursor = conn.cursor(dictionary=True)

    # ✅ Query hardcoded (tidak ada input user) = AMAN dari SQL Injection
    query = """
        SELECT
            p.Id_product,
            p.description,
            COALESCE(SUM(wai.Weight), 0) AS total_alloc,
            COALESCE(SUM(wci.Weight), 0) AS total_completion,
            (COALESCE(SUM(wai.Weight), 0) - COALESCE(SUM(wci.Weight), 0)) AS selisih
        FROM workallocationitem wai
        JOIN product p ON p.Id_product = wai.FG
        LEFT JOIN workcompletionitem wci
               ON wci.LinkID  = wai.IDM
              AND wci.LinkOrd = wai.Ordinal
        GROUP BY p.Id_product, p.description
        ORDER BY p.Id_product
    """
    cursor.execute(query)
    results = cursor.fetchall()
    cursor.close()
    conn.close()

    return render_template('weight_diff.html', results=results)

if __name__ == '__main__':
    app.run(debug=True, host='127.0.0.1', port=5000)
