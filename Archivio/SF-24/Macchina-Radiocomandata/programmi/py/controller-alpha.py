import serial
ser = serial.Serial("COM3", 9600, timeout = 1) #Change your port name COM... and your baudrate
while(True):
    uInput = input("command:")
    ser.write(bytearray(uInput,'ascii'))
    if(uInput=="10"):
        ser.close()
#it works!!!
