#include <ESP8266WiFi.h>
#include <WiFiClientSecureBearSSL.h>
#include <ESP8266HTTPClient.h>
#include <DHT.h>
#include <time.h>

// =====================================================
// WIFI SETTINGS
// =====================================================

const char* WIFI_SSID = "YOUR_WIFI_NAME";
const char* WIFI_PASSWORD = "YOUR_WIFI_PASSWORD";

// =====================================================
// API
// =====================================================

const char* API_URL =
  "https://www.ashishvegan.com/apps/kopargaon/post-data.php";

// =====================================================
// SENSOR PINS
// =====================================================

#define DHTPIN D4
#define DHTTYPE DHT11
#define SOIL_PIN A0

DHT dht(DHTPIN, DHTTYPE);

// =====================================================
// DEVICE ID
// =====================================================

const char* DEVICE_HASH = "KOPARGAON_ESP8266_001";

// =====================================================
// TIME
// =====================================================

const long GMT_OFFSET_SEC = 19800;   // UTC + 5:30
const int DAYLIGHT_OFFSET_SEC = 0;

// =====================================================
// INTERVAL
// =====================================================

unsigned long lastPostTime = 0;

// 1 minute
const unsigned long POST_INTERVAL = 60000;

// =====================================================
// CONNECT WIFI
// =====================================================

void connectWiFi() {

  Serial.println();
  Serial.println("[WiFi] Connecting...");

  WiFi.mode(WIFI_STA);
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);

  int attempts = 0;

  while (WiFi.status() != WL_CONNECTED && attempts < 30) {

    delay(500);

    Serial.print(".");

    attempts++;
  }

  Serial.println();

  if (WiFi.status() == WL_CONNECTED) {

    Serial.println("[WiFi] Connected!");

    Serial.print("[WiFi] IP Address: ");
    Serial.println(WiFi.localIP());

    Serial.print("[WiFi] Signal: ");
    Serial.print(WiFi.RSSI());
    Serial.println(" dBm");

  } else {

    Serial.println("[WiFi] Connection FAILED!");
  }
}

// =====================================================
// GET DATE & TIME
// =====================================================

String getDateTime() {

  time_t now = time(nullptr);

  if (now < 100000) {
    return "00-00-0000 00:00 AM";
  }

  struct tm* timeinfo = localtime(&now);

  char buffer[30];

  strftime(
    buffer,
    sizeof(buffer),
    "%d-%m-%Y %I:%M %p",
    timeinfo
  );

  return String(buffer);
}

// =====================================================
// SEND SENSOR DATA
// =====================================================

void sendSensorData(
  float temperature,
  float humidity,
  int soilRaw,
  int soilMoisture,
  String soilStatus
) {

  if (WiFi.status() != WL_CONNECTED) {

    Serial.println("[API] WiFi disconnected!");
    connectWiFi();

    if (WiFi.status() != WL_CONNECTED) {
      Serial.println("[API] Cannot send data.");
      return;
    }
  }

  Serial.println();
  Serial.println("================================");
  Serial.println("[API] Sending sensor data...");
  Serial.println("================================");

  // ---------------------------------------------------
  // HTTPS CLIENT
  // ---------------------------------------------------

  std::unique_ptr<BearSSL::WiFiClientSecure> client(
    new BearSSL::WiFiClientSecure
  );

  /*
     For testing:
     This accepts the HTTPS certificate without validation.

     For production, certificate validation should be
     configured using the server CA certificate.
  */

  client->setInsecure();

  // ---------------------------------------------------
  // HTTP
  // ---------------------------------------------------

  HTTPClient https;

  Serial.print("[API] URL: ");
  Serial.println(API_URL);

  if (!https.begin(*client, API_URL)) {

    Serial.println("[API] HTTPS connection failed!");
    return;
  }

  https.addHeader("Content-Type", "application/json");

  // ---------------------------------------------------
  // DATE / TIME
  // ---------------------------------------------------

  String dateTime = getDateTime();

  // ---------------------------------------------------
  // JSON
  // ---------------------------------------------------

  String jsonData = "{";

  jsonData += "\"hash_id\":\"";
  jsonData += DEVICE_HASH;
  jsonData += "\",";

  jsonData += "\"sensor1\":";
  jsonData += String(temperature, 2);
  jsonData += ",";

  jsonData += "\"sensor2\":";
  jsonData += String(humidity, 2);
  jsonData += ",";

  jsonData += "\"sensor3\":";
  jsonData += String(soilRaw);
  jsonData += ",";

  jsonData += "\"sensor4\":";
  jsonData += String(soilMoisture);
  jsonData += ",";

  jsonData += "\"sensor5\":\"";
  jsonData += soilStatus;
  jsonData += "\"";

  jsonData += "}";

  Serial.println("[API] JSON:");
  Serial.println(jsonData);

  // ---------------------------------------------------
  // POST
  // ---------------------------------------------------

  int httpCode = https.POST(jsonData);

  Serial.print("[API] HTTP Response: ");
  Serial.println(httpCode);

  if (httpCode > 0) {

    String response = https.getString();

    Serial.println("[API] Server Response:");
    Serial.println(response);

  } else {

    Serial.print("[API] POST Error: ");
    Serial.println(https.errorToString(httpCode));
  }

  https.end();

  Serial.println("================================");
}

// =====================================================
// SETUP
// =====================================================

void setup() {

  Serial.begin(115200);

  delay(1000);

  Serial.println();
  Serial.println("==============================");
  Serial.println(" AGRICULTURE MONITORING SYSTEM");
  Serial.println("==============================");

  // ---------------------------------------------------
  // DHT
  // ---------------------------------------------------

  Serial.println("[1] Starting DHT11...");

  dht.begin();

  Serial.println("[OK] DHT11 initialized");

  // ---------------------------------------------------
  // SOIL
  // ---------------------------------------------------

  Serial.println("[2] Starting Soil Sensor...");

  pinMode(SOIL_PIN, INPUT);

  Serial.println("[OK] Soil sensor initialized");

  // ---------------------------------------------------
  // WIFI
  // ---------------------------------------------------

  Serial.println("[3] Starting WiFi...");

  connectWiFi();

  // ---------------------------------------------------
  // NTP
  // ---------------------------------------------------

  Serial.println("[4] Synchronizing time...");

  configTime(
    GMT_OFFSET_SEC,
    DAYLIGHT_OFFSET_SEC,
    "pool.ntp.org",
    "time.nist.gov",
    "time.google.com"
  );

  Serial.println("[OK] Time synchronization started");

  // Wait for time

  time_t now = time(nullptr);

  int retry = 0;

  while (now < 100000 && retry < 20) {

    delay(500);

    Serial.print(".");

    now = time(nullptr);

    retry++;
  }

  Serial.println();

  Serial.print("[TIME] ");
  Serial.println(getDateTime());

  Serial.println();
  Serial.println("[OK] System Ready!");
  Serial.println();
}

// =====================================================
// LOOP
// =====================================================

void loop() {

  // ---------------------------------------------------
  // WIFI CHECK
  // ---------------------------------------------------

  if (WiFi.status() != WL_CONNECTED) {

    Serial.println("[WiFi] Disconnected!");
    connectWiFi();
  }

  // ---------------------------------------------------
  // READ DHT11
  // ---------------------------------------------------

  float humidity = dht.readHumidity();
  float temperature = dht.readTemperature();

  // ---------------------------------------------------
  // READ SOIL
  // ---------------------------------------------------

  int soilRaw = analogRead(SOIL_PIN);

  int soilMoisture = map(
    soilRaw,
    1023,
    300,
    0,
    100
  );

  soilMoisture = constrain(
    soilMoisture,
    0,
    100
  );

  // ---------------------------------------------------
  // SOIL STATUS
  // ---------------------------------------------------

  String soilStatus;

  if (soilMoisture < 30) {

    soilStatus = "DRY";

  } else if (soilMoisture < 60) {

    soilStatus = "MODERATE";

  } else {

    soilStatus = "WET";
  }

  // ---------------------------------------------------
  // DISPLAY
  // ---------------------------------------------------

  Serial.println();
  Serial.println("========== SENSOR DATA ==========");

  if (isnan(temperature)) {

    Serial.println("Temperature : ERROR");

  } else {

    Serial.print("Temperature : ");
    Serial.print(temperature);
    Serial.println(" °C");
  }

  if (isnan(humidity)) {

    Serial.println("Humidity    : ERROR");

  } else {

    Serial.print("Humidity    : ");
    Serial.print(humidity);
    Serial.println(" %");
  }

  Serial.print("Soil Raw    : ");
  Serial.println(soilRaw);

  Serial.print("Soil Moist. : ");
  Serial.print(soilMoisture);
  Serial.println(" %");

  Serial.print("Soil Status : ");
  Serial.println(soilStatus);

  Serial.print("Date/Time   : ");
  Serial.println(getDateTime());

  Serial.println("=================================");

  // ---------------------------------------------------
  // POST EVERY 1 MINUTE
  // ---------------------------------------------------

  if (lastPostTime == 0 ||
      millis() - lastPostTime >= POST_INTERVAL) {

    // Only send valid DHT readings

    if (!isnan(temperature) && !isnan(humidity)) {

      sendSensorData(
        temperature,
        humidity,
        soilRaw,
        soilMoisture,
        soilStatus
      );

    } else {

      Serial.println("[API] DHT data invalid. Not sending.");
    }

    lastPostTime = millis();
  }

  // Small delay
  delay(2000);
}