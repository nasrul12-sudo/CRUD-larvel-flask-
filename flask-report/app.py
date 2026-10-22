from flask import Flask, render_template, jsonify
from sqlalchemy import func
from database import SessionLocal
from models import Product, WorkAllocationItem, WorkCompletionItem

app = Flask(__name__)

def get_weight_diff_data():
    session = SessionLocal()
    try:
        total_alloc = func.coalesce(func.sum(WorkAllocationItem.Weight), 0).label('total_alloc')
        total_completion = func.coalesce(func.sum(WorkCompletionItem.Weight), 0).label('total_completion')

        query = (
            session.query(
                Product.Id_product.label('Id_product'),
                func.coalesce(Product.custom_description, Product.description).label('description'),
                func.coalesce(Product.custom_serial_no, Product.serial_no).label('serial_no'),
                func.coalesce(Product.custom_carat, Product.carat).label('carat'),
                Product.sub_category.label('sub_category'),
                total_alloc,
                total_completion,
                (total_alloc - total_completion).label('selisih'),
            )
            .join(WorkAllocationItem, WorkAllocationItem.FG == Product.Id_product)
            .outerjoin(
                WorkCompletionItem,
                (WorkCompletionItem.LinkID == WorkAllocationItem.IDM)
                & (WorkCompletionItem.LinkOrd == WorkAllocationItem.Ordinal),
            )
            .group_by(
                Product.Id_product,
                Product.custom_description,
                Product.description,
                Product.custom_serial_no,
                Product.serial_no,
                Product.custom_carat,
                Product.carat,
                Product.sub_category,
            )
            .order_by(Product.sub_category, Product.Id_product)
        )

        results = query.all()
        return [
            {
                'Id_product': row.Id_product,
                'description': row.description,
                'serial_no': row.serial_no,
                'carat': row.carat,
                'sub_category': row.sub_category,
                'total_alloc': float(row.total_alloc),
                'total_completion': float(row.total_completion),
                'selisih': float(row.selisih),
            }
            for row in results
        ]
    finally:
        session.close()

@app.route('/')
def index():
    return '<a href="/weight-diff">Lihat Selisih Berat per Produk</a>'

@app.route('/weight-diff')
def weight_diff():
    return render_template('weight_diff.html', results=get_weight_diff_data())

@app.route('/api/weight-diff')
def api_weight_diff():
    return jsonify(get_weight_diff_data())

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5000)