#include <avr/wdt.h>
#include <SoftwareSerial.h>
#define VCCBLU 12  //pin per hc05
#define GNDBLU 11
SoftwareSerial mySerial(10, 9);

//pin dei motori

//dietro sinista
#define MOTORI 7

void setup() {
  Serial.begin(9600);    // inizializza la comunicazione seriale a 9600 bps
  mySerial.begin(9600);  //creo la mia seriale digitale
  pinMode(VCCBLU, OUTPUT);
  pinMode(GNDBLU, OUTPUT);
  digitalWrite(VCCBLU, HIGH);  //accendo hc05
  digitalWrite(GNDBLU, LOW);
  while (!Serial) { ; }  //attendo che si stabilisca la connessione
  wdt_enable(WDTO_1S);

  //setto i pin dei motiri come output
  pinMode(MOTORI, OUTPUT);
}

void loop() {
  wdt_reset();
  if (mySerial.available() > 0) {         // se ci sono dati disponibili sulla seriale
    char incomingByte = mySerial.read();  // leggi un carattere dalla seriale
    if (incomingByte == '2') {            // se il carattere ricevuto è '2'
      avanti();                           //tutti accesi
      delay(200);
      wdt_reset();
      //spenti();  //tutti i motori spenti
    } else if (incomingByte == '1') {
      spenti();  //motori spenti
      wdt_reset();
    } else if (incomingByte == '3') {
      //destra();  //motori destra
      delay(200);
      wdt_reset();
      spenti();  //motori spenti
    } else if (incomingByte == '4') {
      //sinistra();  //motori sinistra
      delay(200);
      wdt_reset();
      spenti();  //motori spenti
    } else {
      spenti();  //motori spenti
      wdt_reset();
    }
  }
  while (mySerial.available() > 0) {  //pulisco la seriale
    wdt_reset();
    char incomingByte = mySerial.read();
  }
}

void avanti() {
  digitalWrite(MOTORI, HIGH);
  return;
}
/*
void sinistra() {
  digitalWrite(SX, LOW);
  digitalWrite(DX, HIGH);
  return;
}

void destra() {
  digitalWrite(SX, HIGH);
  digitalWrite(DX, LOW);
  return;
}*/

void spenti() {
  digitalWrite(MOTORI, LOW);
  return;
}
