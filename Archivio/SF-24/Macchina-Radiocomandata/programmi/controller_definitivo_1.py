#controller definitivo
import serial
from tkinter import *
import sys

ser = serial.Serial("COM3", 9600, timeout=1)

window = Tk()

def avanti(event):
    print("avanti")
    ser.write(bytearray('2', 'ascii'))

def indietro(event):
    print("indietro")
    ser.write(bytearray('1', 'ascii'))

def sinistra(event):
    print("sinistra")
    ser.write(bytearray('3', 'ascii'))
    
def destra(event):
    print("destra")
    ser.write(bytearray('4', 'ascii'))
    
def destra_avanti(event):
    print("destra-avanti")
    ser.write(bytearray('6', 'ascii'))
    
def sinistra_avanti(event):
    print("sinistra-avanti")
    ser.write(bytearray('5', 'ascii'))
    
def none(event):
    print("none")
    ser.close()
    window.destroy()
    sys.exit(0)
    
window.bind("<w>", avanti)
window.bind("<s>", indietro)
window.bind("<a>", sinistra)
window.bind("<d>", destra)
window.bind("<e>", destra_avanti)
window.bind("<q>", sinistra_avanti)
window.bind("<c>", none)

window.mainloop()
