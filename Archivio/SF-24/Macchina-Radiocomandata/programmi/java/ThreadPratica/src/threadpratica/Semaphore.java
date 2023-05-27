/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package threadpratica;

/**
 *
 * @author Lenovo
 */
public class Semaphore{
    private int counter;
    
    public Semaphore(int n) {
        this.counter=n;
    }

    public synchronized void acquire() throws InterruptedException{
        if(counter==0){
            this.wait();
        }
        counter--;
    }
    
    public synchronized void release(){
        counter++;
        if(counter>0){
            this.notify();
        }
    }
}
