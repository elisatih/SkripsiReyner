#include <SoftwareSerial.h>
#include <OneWire.h>
#include <DallasTemperature.h>

// Data wire dihubungkan ke pin digital 4
#define ONE_WIRE_BUS 4
const int oneWireBus = 4;

SoftwareSerial xbee(19, 18); // RX, TX. Pin untuk menghubungkan xbee dengan arduino

String str_suhu;
String str_kekeruhan;
String str_pH;
String str_out;
String node = "1";
//const int timer = 60000;

OneWire oneWire(ONE_WIRE_BUS);
DallasTemperature sensors(&oneWire);


void setup(void)
{
  pinMode(13,OUTPUT);
  Serial.begin(9600);
  xbee.begin(9600);
  sensors.begin();
}


void loop(void){ 
  int i=0;
  
  //suhu
  sensors.requestTemperatures(); 
  float meanSuhu=0;
  float dataSuhu;

  //Kekeruhan
  int sensorValue = analogRead(A1);// membaca input dari analog pin 1:
  float meanKekeruhan=0;
  float voltage;

  //pH
  int measure = analogRead(A7);// membaca input dari analog pin 7:
  float meanPH=0;
  float voltage2;
  
  unsigned long start = millis ();
  while(millis () - start <= 60000){  //Timer untuk mengambil pemantauan
    //suhu
    dataSuhu = sensors.getTempCByIndex(0);   
    meanSuhu+=dataSuhu;

    //kekeruhan
    voltage = (sensorValue * (5.0 / 1024.0)-0.2); // Mengubah pembacaan digital ke voltage
    meanKekeruhan+=voltage;

    //pH
    voltage2 = (5 / 1024.0 * measure); // Mengubah pembacaan digital ke voltage
    meanPH+= 7 + ((2.82 - voltage2) / 0.18);

    //banyak nilai
    i++;
    Serial.print(".");
 }

 Serial.println();
  meanSuhu=meanSuhu/i;
  meanKekeruhan=meanKekeruhan/i;
  meanPH=meanPH/i;
  
  //convert suhu to str
  str_suhu=String(meanSuhu);
  
  //convert kekeruhan to str
  str_kekeruhan=String(meanKekeruhan);

  //convert pH to str
  str_pH=String(meanPH);
  
  // node|suhu|kekeruhan|ph
  String msg = node+"|"+str_suhu+"|"+str_kekeruhan+"|"+str_pH;

  xbee.print(msg);
  
  Serial.println(msg);
  meanKekeruhan =0;
  meanSuhu=0;
  meanPH=0;
}
