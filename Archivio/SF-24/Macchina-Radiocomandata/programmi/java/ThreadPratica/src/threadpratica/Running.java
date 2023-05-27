/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package threadpratica;

import java.util.logging.Level;
import java.util.logging.Logger;

/**
 *
 * @author Lenovo
 */
class Running extends Thread{
    private Semaphore s;
    
    public Running(Semaphore s){
        this.s=s;
    }
    
    @Override
    public void run(){
        try {
            s.acquire();
        } catch (InterruptedException ex) {
            Logger.getLogger(Running.class.getName()).log(Level.SEVERE, null, ex);
        }
        System.out.println("dentro "+ this.getName());
        try {
            Running.sleep(1500);
        } catch (InterruptedException ex) {
            Logger.getLogger(Running.class.getName()).log(Level.SEVERE, null, ex);
        }
        System.out.println("fuori "+this.getName());
        s.release();
    }
    
}
