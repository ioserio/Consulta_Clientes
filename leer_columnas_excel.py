import pandas as pd

# Leer solo los nombres de las columnas del archivo Excel
excel_path = r'CARTERA DE CLIENTES.xlsx'
df = pd.read_excel(excel_path, nrows=0)
print(list(df.columns))
