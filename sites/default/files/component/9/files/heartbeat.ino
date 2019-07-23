int heartBeat [] = {61, 69, 63, 85, 78, 84, 73, 69, 69, 69, 69, 70, 63, 60, 60, 61, 63, 66, 63, 72, 66, 63, 79, 66, 87, 81, 66, 60, 63, 63, 84, 67, 60, 69, 60, 102, 64, 64, 84, 73, 105, 93, 72, 73, 73, 72, 63, 70, 63, 69, 93, 85, 75, 75, 64, 81, 64, 69, 66, 91, 84, 67, 67, 70, 73, 67, 73, 87, 73, 69, 99, 96, 78, 87, 75, 88, 66, 69, 69, 87, 91, 73, 78, 93, 78, 78, 63, 88, 88, 69, 57, 79, 69, 58, 144, 91, 81, 81, 67, 82, 73, 64, 61, 70, 93, 72, 69, 67, 91, 69, 90, 87, 88, 78, 72, 76, 81, 72, 93, 76, 66, 90, 84, 67, 70, 75, 75, 60, 70, 63, 78, 72, 69, 69, 66, 69, 76, 66, 75, 81, 75, 87, 75, 94, 82, 78, 61, 99, 85, 79, 81, 81, 72, 87, 72, 67, 84, 66, 75, 94, 66, 84, 70, 57, 84, 81, 87, 93, 69, 69, 82, 69, 81, 90, 99, 85, 87, 57, 70, 63, 69, 70, 78, 69, 96, 87, 85, 70, 75, 58, 79, 88, 93, 93, 78, 76, 94, 88, 66, 79, 93, 84, 85, 63, 61, 85, 87, 66, 64, 76, 69, 81, 78, 70, 72, 73, 72, 75, 69, 75, 76, 102, 91, 69, 87, 75, 70, 100, 78, 64, 97, 79, 58, 87, 93, 82, 84, 69};

int timeCount[] = {0, 29, 164, 175, 193, 212, 216, 231, 243, 250, 273, 286, 305, 403, 416, 439, 467, 472, 487, 703, 717, 773, 849, 958, 979, 997, 1018, 1046, 1055, 1239, 1252, 1314, 1322, 1363, 1538, 1642, 1788, 1805, 1851, 2052, 2093, 2125, 2291, 2346, 2385, 2420, 2441, 2455, 2555, 2631, 2706, 2802, 2811, 2821, 2862, 2910, 2969, 2992, 3097, 3122, 3140, 3176, 3193, 3231, 3255, 3364, 3419, 3517, 3524, 3618, 3634, 3659, 3673, 3704, 3755, 3786, 3898, 3930, 3961, 3985, 3994, 4005, 4038, 4060, 4240, 4344, 4458, 4501, 4532, 4544, 4554, 4579, 4695, 4721, 4729, 4768, 4780, 4803, 4824, 4843, 4868, 5003, 5063, 5071, 5091, 5111, 5155, 5241, 5283, 5331, 5423, 5552, 5564, 5575, 5590, 5637, 5661, 5760, 5799, 5820, 5848, 5932, 6069, 6069, 6302, 6365, 6492, 6586, 6631, 6643, 6672, 6702, 6859, 6882, 6906, 6960, 6981, 6791, 7147, 7170, 7205, 7216, 7472, 7519, 7634, 7695, 7716, 7805, 7919, 7999, 8050, 8062, 8185, 8307, 8346, 8444, 8550, 8563, 8589, 8603, 8769, 8878, 8990, 9018, 9095, 9107, 9118, 9281, 9298, 9319, 9348, 9374, 9386, 9422, 9429, 9433, 9527, 9583, 9610, 9625, 9641, 9654, 9679, 9791, 9865, 9877, 9885, 9944, 9953, 10065, 10105, 10145, 10180, 10194, 10239, 10354, 10398, 10424, 10606, 10636, 10660, 10680, 11016, 10876, 10884, 10926, 10951, 11143, 11158, 11177, 11435, 11449, 11684, 11718, 11727, 11792, 11935, 12015, 12044, 12077, 12240, 12254, 12257, 12287, 12318, 12331, 12491, 12523, 12554, 12573, 12653, 12763, 12769, 12798, 12805, 12823, 12843, 13000};
float counter1 = 0;
float counter2 = 0;
float counter3 = 0;
float counter4 = 0;
float counter5 = 0;
float counter6 = 0;

int arrayNum;

int levelMeter1;
int levelMeter2;
int levelMeter3;
int levelMeter4;
int levelMeter5;
int levelMeter6;

float motorP1;
float motorP2;
float motorP3;
float motorP4;
float motorP5;
float motorP6;

int maxMotorPower1 = 255;
int maxMotorPower2 = 255;
int maxMotorPower3 = 255;
int maxMotorPower4 = 255;
int maxMotorPower5 = 255;
int maxMotorPower6 = 255;
int minMotorPower1 = 220;
int minMotorPower2 = 170;
int minMotorPower3 = 210;
int minMotorPower4 = 210;
int minMotorPower5 = 220;
int minMotorPower6 = 220;

int minHeartBeat = 57;
int maxHeartBeat = 144;

float counterStep = 0.5;


void setup() {
  Serial.begin(9600);

  Serial.println("setup----------------------------------------------");
  Serial.println(sizeof(heartBeat) / sizeof(heartBeat[0]) );
  Serial.println(sizeof(timeCount) / sizeof(timeCount[0]));
  Serial.println("----------------------------------------------");
  arrayNum = sizeof(heartBeat) / sizeof(heartBeat[0]);

  // instantiate levelMeters
  levelMeter2 = arrayNum / 6;
  levelMeter3 = 2 * (arrayNum / 6);
  levelMeter4 = 3 * (arrayNum / 6);
  levelMeter5 = 4 * (arrayNum / 6);
  levelMeter6 = 5 * (arrayNum / 6);


  pinMode(A4, OUTPUT);
  pinMode(A5, OUTPUT);
  pinMode(3, OUTPUT); //analogWrite
  //motor_2
  pinMode(2, OUTPUT);
  pinMode(4, OUTPUT);
  pinMode(5, OUTPUT); //analogWrite
  //motor_3
  pinMode(7, OUTPUT);
  pinMode(8, OUTPUT);
  pinMode(6, OUTPUT); //analogWrite
  //motor_4
  pinMode(12, OUTPUT);
  pinMode(13, OUTPUT);
  pinMode(9, OUTPUT); //analogWrite
  //motor_5
  pinMode(A0, OUTPUT);
  pinMode(A1, OUTPUT);
  pinMode(10, OUTPUT); //analogWrite
  //motor_6
  pinMode(A2, OUTPUT);
  pinMode(A3, OUTPUT);
  pinMode(11, OUTPUT); //analogWrite
}





void motor_1() {
  counter1 += counterStep; 

  if (levelMeter1 < arrayNum / 6) {
    if ( timeCount[levelMeter1] < counter1 && counter1 < timeCount[levelMeter1 + 1]) {
      // show array number
      Serial.print("1stage : ");
      Serial.println( levelMeter1 );
      // show heartbeat
      Serial.print("1_heartbeat : ");
      Serial.println( heartBeat[levelMeter1] );

      // timing of rolling
      motorP1 = map(float(heartBeat[levelMeter1]), minHeartBeat, maxHeartBeat, minMotorPower1, maxMotorPower1); //min 57, max 144
      Serial.print("1_power : ");
      Serial.println(motorP1);

      digitalWrite(A4, HIGH);
      digitalWrite(A5, LOW);
      analogWrite(3, motorP1); // speed of rolling


    } else {
      Serial.println("////1 now next stage////");
      levelMeter1 += 1;
    }
  } else {
    levelMeter1 = 0;
    counter1 = 0;
  }
}

void motor_2() {
  counter2 = timeCount[levelMeter2];
  counter2 += counterStep;

  if (levelMeter2 < 2 * ( arrayNum / 6)) {
    if ( timeCount[levelMeter2] < counter2 && counter2 < timeCount[levelMeter2 + 1]) {
      // show array number
      Serial.print("2stage : ");
      Serial.println( levelMeter2 );
      // show heartbeat
      Serial.print("2_heartbeat : ");
      Serial.println( heartBeat[levelMeter2] );

      // timing of rolling
      motorP2 = map(float(heartBeat[levelMeter2]), minHeartBeat, maxHeartBeat, minMotorPower2, maxMotorPower2); //min 57, max 144
      Serial.print("2_power : ");
      Serial.println(motorP2);
      // one direction
      digitalWrite(2, HIGH);
      digitalWrite(4, LOW);
      analogWrite(5, motorP2); // speed of rolling

    } else {
      Serial.println("2 now next stage");
      levelMeter2 += 1;
    }
  } else {
    levelMeter2 = arrayNum / 6;
    counter2 = 0;
  }
}

void motor_3() {
  counter3 = timeCount[levelMeter3];
  counter3 += counterStep;

  if (levelMeter3 < 3 * ( arrayNum / 6 )) {
    if ( timeCount[levelMeter3] < counter3 && counter3 < timeCount[levelMeter3 + 1]) {
      // show array number
      Serial.print("3stage : ");
      Serial.println( levelMeter3 );
      // show heartbeat
      Serial.print("3_heartbeat : ");
      Serial.println( heartBeat[levelMeter3] );

      // timing of rolling
      motorP3 = map(float(heartBeat[levelMeter3]), minHeartBeat, maxHeartBeat, minMotorPower3, maxMotorPower3); //min 57, max 144
      Serial.print("3_power : ");
      Serial.println(motorP3);
      // one direction
      digitalWrite(7, HIGH);
      digitalWrite(8, LOW);
      analogWrite(6, motorP3); // speed of rolling

    } else {
      Serial.println("3 now next stage");
      levelMeter3 += 1;
    }
  } else {
    levelMeter3 = 2 * (arrayNum / 6);
    counter3 = 0;
  }
}

void motor_4() {
  counter4 = timeCount[levelMeter4];
  counter4 += counterStep;

  if (levelMeter4 < 4 * ( arrayNum / 6 ))  {
    if ( timeCount[levelMeter4] < counter4 && counter4 < timeCount[levelMeter4 + 1]) {
      // show array number
      Serial.print("4stage : ");
      Serial.println( levelMeter4 );
      // show heartbeat
      Serial.print("4_heartbeat : ");
      Serial.println( heartBeat[levelMeter4] );

      // timing of rolling
      motorP4 = map(float(heartBeat[levelMeter4]), minHeartBeat, maxHeartBeat, minMotorPower4, maxMotorPower4); //min 57, max 144
      Serial.print("4_power : ");
      Serial.println(motorP4);
      // one direction
      digitalWrite(12, HIGH);
      digitalWrite(13, LOW);
      analogWrite(9, motorP4); // speed of rolling

    } else {
      Serial.println("4 now next stage");
      levelMeter4 += 1;
    }
  } else {
    levelMeter4 = 3 * (arrayNum /  6);
    counter4 = 0;
  }
}

void motor_5() {
  counter5 = timeCount[levelMeter5];
  counter5 += counterStep;

  if (levelMeter5 < 5 * ( arrayNum / 6 ))  {
    if ( timeCount[levelMeter5] < counter5 && counter5 < timeCount[levelMeter5 + 1]) {
      // show array number
      Serial.print("5stage : ");
      Serial.println( levelMeter5 );
      // show heartbeat
      Serial.print("5_heartbeat : ");
      Serial.println( heartBeat[levelMeter5] );

      // timing of rolling
      motorP5 = map(float(heartBeat[levelMeter5]), minHeartBeat, maxHeartBeat, minMotorPower5, maxMotorPower5); //min 57, max 144
      Serial.print("5_power : ");
      Serial.println(motorP5);
      // one direction
      digitalWrite(A0, HIGH);
      digitalWrite(A1, LOW);
      analogWrite(10, motorP5); // speed of rolling

    } else {
      Serial.println("5 now next stage");
      levelMeter5 += 1;
    }
  } else {
    levelMeter5 = 4 * (arrayNum /  6);
    counter5 = 0;
  }
}

void motor_6() {
  counter6 = timeCount[levelMeter6];
  counter6  += counterStep;

  if (levelMeter6 < arrayNum )  {
    if ( timeCount[levelMeter6] < counter6 && counter6 < timeCount[levelMeter6 + 1]) {
      // show array number
      Serial.print("6stage : ");
      Serial.println( levelMeter6 );
      // show heartbeat
      Serial.print("6_heartbeat : ");
      Serial.println( heartBeat[levelMeter6] );

      // timing of rolling
      motorP6 = map(float(heartBeat[levelMeter6]), minHeartBeat, maxHeartBeat, minMotorPower6, maxMotorPower6); //min 57, max 144
      Serial.print("6_power : ");
      Serial.println(motorP6);
      // one direction
      digitalWrite(A2, HIGH);
      digitalWrite(A3, LOW);
      analogWrite(11, motorP6); // speed of rolling

    } else {
      Serial.println("6 now next stage");
      levelMeter6 += 1;
    }
  } else {
    levelMeter6 = 5 * (arrayNum /  6);
    counter6 = 0;
  }
}

void loop() {
    motor_1();
    motor_2();
    motor_3();
    motor_4();
    motor_5();
    motor_6();
    Serial.print("------------------------------------------interval time count : ");
    Serial.println(timeCounter % (pauseSec + workSec));
    Serial.print("------------------------------------------whole working time count: ");
    Serial.println(timeCounter);
}