#include <avr/wdt.h>
#include <Servo.h> // importa la libreria Servo
Servo sx; // crea un oggetto servo
Servo dx;

int default_servo = 90;
int motori = 9; // definisci il pin a cui è collegato il led

void setup() {
  Serial.begin(9600); // inizializza la comunicazione seriale a 9600 bps
  pinMode(motori, OUTPUT); // imposta il pin del led come uscita
  wdt_enable(WDTO_1S);
  sx.attach(10);
  dx.attach(11);
  sx.write(default_servo);
  dx.write(default_servo);
}

void loop() {
  wdt_reset();
  if (Serial.available() > 0) { // se ci sono dati disponibili sulla seriale
    char incomingByte = Serial.read(); // leggi un carattere dalla seriale
    if (incomingByte == '2') { // se il carattere ricevuto è '1'
      sx.write(default_servo);
      dx.write(default_servo);
      digitalWrite(motori, HIGH); // accendi il led
      delay(200);
      wdt_reset();
      digitalWrite(motori, LOW); // accendi il led
    } else if (incomingByte == '1') { // se il carattere ricevuto è '0'
      digitalWrite(motori, LOW); // spegni il led
      sx.write(default_servo);
      dx.write(default_servo);
      wdt_reset();
    } else if(incomingByte == '3'){
      sx.write(default_servo+40);
      dx.write(default_servo+40);
      wdt_reset();
    }else if(incomingByte == '4'){
      sx.write(default_servo-40);
      dx.write(default_servo-40);
      wdt_reset();
    }
     else if(incomingByte == '5'){
      digitalWrite(motori, HIGH); // accendi il led
      sx.write(default_servo+40);
      dx.write(default_servo+40);
      delay(400);
      wdt_reset();
      digitalWrite(motori, LOW); // spegni il led
    }else if(incomingByte == '6'){
      digitalWrite(motori, HIGH); // accendi il led
      sx.write(default_servo-40);
      dx.write(default_servo-40);
      delay(400);
      wdt_reset();
      digitalWrite(motori, LOW); // spegni il led
    }else{
      digitalWrite(motori, LOW); // spegni il led
      sx.write(default_servo);
      dx.write(default_servo);
      wdt_reset();
    }
  }
  while(Serial.available()>0){
    wdt_reset();
    char incomingByte = Serial.read();
  }
}