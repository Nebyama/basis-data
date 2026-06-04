import openpyxl

path = r'D:\Judika Basis Data TI ORESTE.xlsx'
wb = openpyxl.load_workbook(path, data_only=True)
ws = wb['Besson Rank']
rows = list(ws.iter_rows(values_only=True))
print('Total rows:', len(rows), 'Total cols:', max(len(r) for r in rows if r))
for i in range(40, 90):
    print(i+1, rows[i])
