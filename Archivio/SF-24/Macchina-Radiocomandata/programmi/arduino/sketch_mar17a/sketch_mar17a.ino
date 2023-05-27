#define OUT 9
#define OUT2 10
void setup() {
  Serial.begin(9600);
  pinMode(OUT,OUTPUT);
  pinMode(OUT2,OUTPUT);
}

void loop() {
  char n='0';
  n=Serial.read();
  if(n=='1'){
    digitalWrite(OUT,HIGH);
    Serial.print("on\n");
  }else if(n=='0'){
    digitalWrite(OUT,LOW);
    Serial.print("off\n");
  }else if(n=='2'){
    digitalWrite(OUT2,LOW);
    Serial.print("off 2\n");
  }else if(n=='3'){
    digitalWrite(OUT2,HIGH);
    Serial.print("on 2\n");
  }
}