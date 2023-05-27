/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Main.java to edit this template
 */
package threadpratica;

/**
 *
 * @author Lenovo
 */
public class ThreadPratica {
    public static int DIM = 10;
    public static void main(String[] args) throws InterruptedException {
        Semaphore s =new Semaphore(10);
        Running[] r = new Running[DIM];
        for(int i=0;i<DIM;i++){
            r[i]=new Running(s);
            r[i].setName(Integer.toString(i));
            r[i].start();
        }
        
        try{
            for(int i=0;i<DIM;i++){
                r[i].join(500);
            }
        }catch(InterruptedException e){
            System.out.println("suca");
        }
    }
}
