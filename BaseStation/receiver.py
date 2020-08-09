import concurrent.futures 
import datetime
import serial
import RPi.GPIO as GPIO
import mysql.connector 
from mysql.connector import Error

GPIO.setmode(GPIO.BCM)
GPIO.setwarnings(False)
#GPIO.setup(23,GPIO.OUT)
ser = serial.Serial(
    port='/dev/ttyS0',
    baudrate = 9600,
    timeout=1            
 )
        

def nerimadata(x):
    parsed = x.split("|")
    if len(parsed) > 1:
        node = parsed[0]
        suhu = parsed[1]
        kekeruhan = parsed[2]
        pH = parsed[3]
        #print(node,kekeruhan,suhu)
        localtime=datetime.datetime.now()
        localtime=localtime.strftime('%Y-%m-%d %H:%M:%S')
        #print(localtime)
        #time.sleep(1)
        return node,pH,suhu,kekeruhan,localtime
        
POOL_SIZE = 5 # tergantung node ada brp
        
def kirimdb(x):
    db = mysql.connector.connect(
        host='localhost',
        database='skripsi',
        user='admin',
        password='root',
        pool_name='mypool',
        pool_size=POOL_SIZE+1
    )
    cursor = db.cursor(buffered=True)
    node = x[0]
    pH = x[1]
    suhu = x[2]
    kekeruhan = x[3]
    localtime = x[4]
    query_insert = ("INSERT INTO data_sensor (waktu_tanggal, idSensor, nilai_pH, nilai_suhu, nilai_kekeruhan) VALUES (%s, %s, %s, %s, %s)")
    query_values = (localtime, node, pH, suhu, kekeruhan)
    cursor.execute(query_insert, query_values)
    db.commit()
    cursor.close()
    db.close()


while 1:
    x=ser.readline().decode("ascii").strip()
    with concurrent.futures.ThreadPoolExecutor() as executor:
        f1 = executor.submit(nerimadata,x)
        print(f1.result())
        if f1.result() != None:
            f2 = executor.submit(kirimdb, f1.result())
            print(f2.result())