int latchPin = 3;
int dataPin = 4;
int clockPin = 2;
int pH_1=0;
float pH_2=0;
float pH=0;
 
byte switchVar1 = 0;
byte switchVar2 = 0;
 
void setup() {
  Serial.begin(9600);
 
  pinMode(latchPin, OUTPUT);
  pinMode(clockPin, OUTPUT);
  pinMode(dataPin, INPUT);
 
}
 
void loop() {
 
  digitalWrite(latchPin,1);
  delayMicroseconds(20);
  digitalWrite(latchPin,0);
 
  switchVar1 = shiftIn(dataPin, clockPin);
  switchVar2 = shiftIn(dataPin, clockPin);
 
 // Serial.println(switchVar1, BIN);
//Serial.println(switchVar2, BIN);
 
// Translating the first two digits
  switch (switchVar1) {
  case B00000110:
    pH_1=1;
    break;
  case B01011011:
    pH_1=2;
    break;
  case B01001111:
    pH_1=3;
    break;
  case B01100110:
    pH_1=4;
    break;    
  case B01101101:
    pH_1=5;
    break;        
  case B01111101:
    pH_1=6;
    break;    
  case B00000111:
    pH_1=7;
    break;    
    case B01111111:
    pH_1=8;
    break;    
    case B01101111:
    pH_1=9;
    break;    
    case B00111111:
    pH_1=0;
    break;    
        case B10111111:
    pH_1=10;
    break;  
      case B10000110:
    pH_1=11;
    break;
  case B11011011:
    pH_1=12;
    break;
  case B11001111:
    pH_1=13;
    break;
  case B11100110:
    pH_1=14;
    break;    
  case B11101101:
    pH_1=15;
    break;        
  case B11111101:
    pH_1=16;
    break;    
  case B10000111:
    pH_1=17;
    break;    
    case B11111111:
    pH_1=18;
    break;    
    case B11101111:
    pH_1=19;
  default:
    pH_1=-1;
  }
 
// Translating the digit after the decimal
 switch (switchVar2) {
    case B00000110:
    pH_2=0.1;
    break;
  case B01011011:
    pH_2=0.2;
    break;
  case B01001111:
    pH_2=0.3;
    break;
  case B01100110:
    pH_2=0.4;
    break;    
  case B01101101:
    pH_2=0.5;
    break;        
  case B01111101:
    pH_2=0.6;
    break;    
  case B00000111:
    pH_2=0.7;
    break;    
    case B01111111:
    pH_2=0.8;
    break;    
    case B01101111:
    pH_2=0.9;
    break;    
    case B00111111:
    pH_2=0.0;
    break;  
  default:
    pH_2=-1;
  }
 
// Calculating pH  
pH = pH_1+pH_2;
// Avoiding garbage
if (pH>0){
Serial.print("pH Integer:  ");  
Serial.print(pH_1);  
Serial.print("     pH Decimal:  ");  
Serial.print(pH_2);
Serial.print("          pH Measured:  ");  
Serial.println(pH,1);
} else {
  pH = 0;
}
delay(100);
}
 
byte shiftIn(int myDataPin, int myClockPin) {
  int i;
  int temp = 0;
  int pinState;
  byte myDataIn = 0;
 
  pinMode(myClockPin, OUTPUT);
  pinMode(myDataPin, INPUT);
 
  for (i=7; i>=0; i--)
  {
    digitalWrite(myClockPin, 0);
   delayMicroseconds(10);
    temp = digitalRead(myDataPin);
    if (temp) {
      pinState = 1;
      myDataIn = myDataIn | (1 << i);
    }
    else {
      pinState = 0;
    }
    digitalWrite(myClockPin, 1);
  }
  return myDataIn;
}