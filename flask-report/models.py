from sqlalchemy import Column, Integer, String, Numeric, ForeignKey
from sqlalchemy.orm import relationship
from database import Base  # ⭐ Ini yang benar: import dari database


class Product(Base):
    __tablename__ = 'product'

    Id_product = Column(Integer, primary_key=True)
    sub_category = Column(String(20), nullable=False)
    serial_no = Column(Integer, nullable=False)
    description = Column(String(255), nullable=False)
    carat = Column(String(10), nullable=False)

    custom_description = Column(String(255))
    custom_serial_no = Column(String(50))
    custom_carat = Column(String(20))

    allocation_items = relationship('WorkAllocationItem', back_populates='product')


class WorkAllocationItem(Base):
    __tablename__ = 'workallocationitem'

    IDM = Column(Integer, primary_key=True)
    Ordinal = Column(Integer, primary_key=True)
    Qty = Column(Integer)
    Weight = Column(Numeric(15, 2))
    FG = Column(Integer, ForeignKey('product.Id_product'))

    product = relationship('Product', back_populates='allocation_items')


class WorkCompletionItem(Base):
    __tablename__ = 'workcompletionitem'

    IDM = Column(Integer, primary_key=True)
    Ordinal = Column(Integer, primary_key=True)
    Qty = Column(Integer)
    Weight = Column(Numeric(15, 2))
    LinkID = Column(Integer)
    LinkOrd = Column(Integer)
    FG = Column(Integer, ForeignKey('product.Id_product'))