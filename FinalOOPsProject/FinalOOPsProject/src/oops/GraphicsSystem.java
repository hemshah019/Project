package oops;

	import java.awt.Color;
	import java.awt.FlowLayout;
	import java.awt.Font;
	import java.awt.image.BufferedImage;
	import java.io.File;
	import java.io.IOException;
	import javax.imageio.ImageIO;
	import uk.ac.leedsbeckett.oop.LBUGraphics;
	import java.io.FileWriter;
	import java.util.ArrayList;
	import java.util.Arrays;
	import javax.swing.*;
	import java.io.BufferedWriter;
	import java.util.Scanner;


	public class GraphicsSystem extends LBUGraphics {
		
		private static final long serialVersionUID = 1L;
		ArrayList<String> cmdlist = new ArrayList<String>();

		public GraphicsSystem()
	    {
	            JFrame JF = new JFrame("TURTLE GUI");
	            JF.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
	            JF.setLayout(new FlowLayout());    
	            JF.add(this);        
	            JF.setSize(825,450);             
	            JF.setVisible(true);
	            penDown();
	            
	    }
		
		    //	Requirement 4
		    //	Method for saving image to png file.
		    public void saveImage() {
		    	BufferedImage image = getBufferedImage();
		        JFileChooser fileChooser = new JFileChooser();
		        int saveDialogResult = fileChooser.showSaveDialog(null);
		        if (saveDialogResult == JFileChooser.APPROVE_OPTION) {
		            File fileToSave = fileChooser.getSelectedFile();
		            try {
		                ImageIO.write(image, "png", fileToSave);
		                JOptionPane.showMessageDialog(null, "Image saved successfully!");
		            } catch (IOException ex) {
		                JOptionPane.showMessageDialog(null, "Error saving image: " + ex.getMessage());
		            }
		        }
		    }
		    
		    //	Method for loading image from file.
		    public void loadImage() {
		        JFileChooser fileChooser = new JFileChooser();
		        int openDialogResult = fileChooser.showOpenDialog(null);
		        if (openDialogResult == JFileChooser.APPROVE_OPTION) {
		            File fileToLoad = fileChooser.getSelectedFile();
		            try {
		            	BufferedImage image = ImageIO.read(fileToLoad);
				    	setBufferedImage(image);
		                JOptionPane.showMessageDialog(null, "Image loaded successfully!");
		            } catch (IOException ex) {
		                JOptionPane.showMessageDialog(null, "Error loading image: " + ex.getMessage());
		            }
		        }
		    }
		    
		    //	Method for saving commands to text file.
		    public void saveCommands() {
		        JFileChooser fileChooser = new JFileChooser();
		        int saveDialogResult = fileChooser.showSaveDialog(null);
		        if (saveDialogResult == JFileChooser.APPROVE_OPTION) {
		            File fileToSave = fileChooser.getSelectedFile();
		            try {
		                FileWriter fwrite = new FileWriter(fileToSave);
		                BufferedWriter writer = new BufferedWriter(fwrite);
		                for (String str : cmdlist) {
		                	writer.write(str +"\n");
		                }
		                writer.close();
		                JOptionPane.showMessageDialog(null, "Commands saved successfully!");
		            } catch (IOException ex) {
		                JOptionPane.showMessageDialog(null, "Error saving commands: " + ex.getMessage());
		            }
		        }
		    }
		    
		    //	Method for loading commands from file and executing them.
		    public void loadCommands() {
		        JFileChooser fileChooser = new JFileChooser();
		        int openDialogResult = fileChooser.showOpenDialog(null);
		        if (openDialogResult == JFileChooser.APPROVE_OPTION) {
		        	File fileToLoad = fileChooser.getSelectedFile();
		            try {
		            	Scanner reader = new Scanner(fileToLoad);
		            	while(reader.hasNextLine()) {
		            		String data = reader.nextLine();
		            		processCommand(data);
		            	}
		            	reader.close();
		                JOptionPane.showMessageDialog(null, "Commands loaded and executed successfully!");
		            } catch (IOException ex) {
		                JOptionPane.showMessageDialog(null, "Error loading commands: " + ex.getMessage());
		            }
		        }
		    }
		    
//			Requirement 5
			@Override
			public void about() {
				super.about();
		        JLabel label = new JLabel("Hem Bikram Sah");
		        label.setBounds(290, 200, 200, 200);
		        label.setForeground(Color.WHITE);
		        Font font = new Font("Arial", Font.BOLD, 24); 
		        label.setFont(font);
		        this.add(label);
		        try {
		        	Thread.sleep(7000);
		        }
		        catch(InterruptedException e) {
		        	e.printStackTrace();
		        }
		        this.remove(label);
			}
			
				//	Method to draw a square with the given length
			    public void square(int length) {
			        for (int i = 0; i < 4; i++) {
			            forward(length);
			            turnRight(90);
			        }
			    }

			    //	Method to set the pen color with RGB values
			    public void pencolour(int red, int green, int blue) {
			    	if (red < 0 || red > 255 || green < 0 || green > 255 || blue < 0 || blue > 255) {
		            	JOptionPane.showMessageDialog(this,"RGB values must be between 0 and 255"); 
			        }
			        Color color = new Color(red, green, blue);
			        setPenColour(color);
			    }

			    //	Method to set the pen width
			    public void penwidth(int width) {
			    	setStroke(width);
			    }

			    //	Method to draw an equilateral triangle with the given size.
			    public void equtriangle(int size) {
			        for (int i = 0; i < 3; i++) {
			            forward(size);
			            turnRight(120);
			        }
			    }

			    //	Method to draw a triangle with the given sides.
			    public void sidetriangle(int side1, int side2, int side3) {
			        double angle1, angle2, angle3;
			        angle1 = Math.acos((Math.pow(side2, 2) + Math.pow(side3, 2) - Math.pow(side1, 2)) / (2 * side2 * side3));
			        angle2 = Math.acos((Math.pow(side1, 2) + Math.pow(side3, 2) - Math.pow(side2, 2)) / (2 * side1 * side3));
			        angle3 = Math.acos((Math.pow(side1, 2) + Math.pow(side2, 2) - Math.pow(side3, 2)) / (2 * side1 * side2));

			        int a = (int) ((angle1 * 180) / Math.PI);
			        int b = (int) ((angle2 * 180) / Math.PI);
			        int c = (int) ((angle3 * 180) / Math.PI);

			        forward(side1);
			        turnLeft(180 + c);
			        forward(side2);
			        turnLeft(180 + a);
			        forward(side3);
			        turnLeft(180 + b);
			    }
			    
			    //	Method to reset pen color and pen width.
			    @Override
			    public void reset() {
			    	super.reset();
			        setPenColour(Color.RED);
			        penwidth(1);
			        penDown();
			    }
			    
		    
		    //	Requirement 6
		    //	Method to write my Name with dancing turtle.
			public void myname() {
				//	H
				penUp();
				turnRight(90);
				forward(250);
				turnRight(90);
				forward(100);
				pencolour(255,0,0);
				penDown();
				forward(-200);
				turnRight(90);
				forward(30);
				turnLeft(90);
				forward(80);
				turnRight(90);
				forward(90);
				turnRight(90);
				forward(80);
				turnLeft(90);
				forward(30);
				turnLeft(90);
				forward(200);
				turnLeft(90);
				forward(30);
				turnLeft(90);
				forward(80);
				turnRight(90);
				forward(90);
				turnRight(90);
				forward(80);
				turnLeft(90);
				forward(30);
				turnRight(180);
				penUp();
				
				//	E
				forward(180);
				turnRight(90);
				pencolour(0,255,0);
				penDown();
				forward(200);
				turnLeft(90);
				forward(150);
				turnLeft(90);
				forward(30);
				turnLeft(90);
				forward(120);
				turnRight(90);
				forward(50);
				turnRight(90);
				forward(90);
				turnLeft(90);
				forward(30);
				turnLeft(90);
				forward(90);
				turnRight(90);
				forward(50);
				turnRight(90);
				forward(120);
				turnLeft(90);
				forward(40);
				turnLeft(90);
				forward(150);
				turnRight(180);
				penUp();
				
				//	M
				forward(180);
				pencolour(0,0,255);
				penDown();
				turnRight(90);
				forward(200);
				penUp();
				forward(-200);
				turnLeft(90);
				turnRight(45);
				penDown();
				forward(120);
				turnLeft(90);
				forward(120);
				turnRight(90);
				turnRight(45);
				forward(200);
				turnRight(90);
				forward(30);
				turnRight(90);
				forward(120);
				turnLeft(90);
				turnLeft(45);
				forward(75);
				turnRight(90);
				forward(75);
				turnLeft(45);
				turnLeft(90);
				forward(120);
				turnRight(90);
				forward(30);
				penUp();
				forward(100);
				turnRight(90);
				forward(-50);
			}
			
			//	Method to draw star with dancing turtle.
			public void star(int size) {
		        for (int i = 0; i < 5; i++) {
		            forward(size);
		            turnLeft(144);
		            forward(size);
		            turnLeft(-72);
		        }
			}
			
	    public void processCommand(String command) {
	        String[] ind = command.split(" ");
	        String cmd = ind[0].toLowerCase();
	        
	        //	Requirement 3        
	        //	Reports invalid commands.
	        String[] validCommands = {"about", "penup", "pendown", "turnleft", "turnright", "forward", "backward", "black", "green", "red", "white", "reset", "clear", "square", "pencolour", "penwidth", "equtriangle", "sidetriangle", "saveimage", "loadimage", "savecommands", "loadcommands", "myname", "circle", "star"};

	        if (Arrays.asList(validCommands).indexOf(ind[0]) == -1) { 
	            JOptionPane.showMessageDialog(this,"Error: Invalid command."); 
	        }

	        //	Detects valid command with missing parameter.
	        String[] TwoParams = {"forward", "backward", "turnleft", "turnright", "square", "equtriangle", "sidetriangle", "star"};
	        if (Arrays.asList(TwoParams).contains(ind[0]) && ind.length < 2) {
	            JOptionPane.showMessageDialog(this, "Error: Missing parameter.");
	        }

	        //	Detects non numeric data for a parameter.
	        int Per = 0;
	        if (ind.length == 2) {
	            if (!ind[1].matches("\\d+")) { // check if second parameter is not a valid integer
	                JOptionPane.showMessageDialog(this, "Error: Non-numeric parameter.");
	            } else {
	                Per = Integer.parseInt(ind[1]);
	                System.out.println(Per);
	            }
	        }

	        //	Correctly bounds parameters.
	        if ((ind[0].equals("forward") || ind[0].equals("backward")) && ind.length == 2) {
	            try {
	                int parameter = Integer.parseInt(ind[1]);
	                if (parameter < 0) {
//	                    JOptionPane.showMessageDialog(this, "Error: Invalid parameter value.");
	                }
	            } catch (NumberFormatException e) { 
	                JOptionPane.showMessageDialog(this, "Error: Non-numeric parameter.");
	            }
	        }

	        //	Requirement 2        
	        //	Executing all method in Process Command.
	        if (cmd.equals("about")) {
	        	about();
	        	cmdlist.add(command);
//	        	displayMessage("Hem Bikram Sah");
	        }
	        
	        else if(cmd.equals("penup")) {
	        	penUp();
	        	cmdlist.add(command);
	        }
	        	
	        else if(cmd.equals("pendown")) {
	        	penDown();
	        	cmdlist.add(command);
	        	}
	        
	        else if (cmd.equals("clear")) {
	        	clear();
	        	cmdlist.add(command);
	        	}
	        
	        else if (cmd.equals("reset")) {
	        	reset();
	        	cmdlist.add(command);
	        }
	        
	        else if (cmd.equals("turnleft"))
	        	if (ind.length == 2) {
                    int degrees = Integer.parseInt(ind[1]);
                    turnLeft(degrees);
                    cmdlist.add(command);
	        	}else {
	        		System.out.println("Invalid command. Usage: turnleft <distance>");
	        	}
	        
	        else if(cmd.equals("turnright"))
	        	if (ind.length == 2) {
                    int degrees = Integer.parseInt(ind[1]);
                    turnRight(degrees);
                    cmdlist.add(command);
                }
	        	else {
	        		System.out.println("Invalid command. Usage: turnright <distance>");
	        	}
	        
	        else if (cmd.equals("forward"))
	        	if (ind.length == 2) {
                    int distance = Integer.parseInt(ind[1]);
                    forward(distance);
                    cmdlist.add(command);
                }
	        	else {
	        		System.out.println("Invalid command. Usage: forward <distance>");
	        	}
	        
	        else if (cmd.equals("backward"))
	        	if (ind.length == 2) {
                    int distance = Integer.parseInt(ind[1]);
                    forward(-distance);
                    cmdlist.add(command);
                }
	        	else {
	        		System.out.println("Invalid command. Usage: backward <distance>");
	        	}
	        
	        else if (cmd.equals("black")) {
	        	setPenColour(Color.BLACK);
	        	cmdlist.add(command);
	        }
	        
	        else if (cmd.equals("green")) {
	        	setPenColour(Color.GREEN);
	        	cmdlist.add(command);
	        }
	        
	        else if (cmd.equals("red")) {
	        	setPenColour(Color.RED);
	        	cmdlist.add(command);
	        }
	        
	        else if (cmd.equals("white")) {
            	setPenColour(Color.WHITE);
            	cmdlist.add(command);
	        }
	        
	        //	Requirement 4
	        else if (cmd.equals("saveimage")) {
	        	saveImage();
	        //	cmdlist.add(command);
	        }
	        
	        else if (cmd.equals("loadimage")) {
	        	loadImage();
	        //	cmdlist.add(command);
	        }
	        
	        else if (cmd.equals("savecommands")) {
	        //	cmdlist.add(command);
	        	saveCommands();
	        }
	        
	        else if (cmd.equals("loadcommands")) {
	        	loadCommands();
	        //	cmdlist.add(command);
	        }
	        
	        //	Requirement 5        
	        else if (cmd.equals("square")) {
	            int length = Integer.parseInt(ind[1]);
	            penDown();
	            square(length);
	            cmdlist.add(command);
	        }
	        
	        else if (cmd.equals("pencolour")) {
	            int red = Integer.parseInt(ind[1]);
	            int green = Integer.parseInt(ind[2]);
	            int blue = Integer.parseInt(ind[3]);
	            pencolour(red, green, blue);
	            cmdlist.add(command);
	        }
	        
	        else if (cmd.equals("penwidth")) {
	            int width = Integer.parseInt(ind[1]);
	            setStroke(width);
	            cmdlist.add(command);
	        }
	        
	        else if (cmd.equals("equtriangle")) {
	            if (ind.length == 2) {
	                int size = Integer.parseInt(ind[1]);
	                penDown();
	                equtriangle(size);
	                cmdlist.add(command);
	            }
	        }
	        else if (cmd.equals("sidetriangle")) {
	        	int side1 = Integer.parseInt(ind[1]);
	        	int side2 = Integer.parseInt(ind[2]);
	        	int side3 = Integer.parseInt(ind[3]);
	        	penDown();
	        	sidetriangle(side1,side2,side3);
	        	cmdlist.add(command);
	        }
	        
	        //	Requirement 6
	        else if (cmd.equals("myname")) {
            	myname();
            	cmdlist.add(command);
	        }
	        
	        else if (cmd.equals("circle")) {
	        	 if (ind.length == 2) {
	                 int radius = Integer.parseInt(ind[1]);
	                 penDown();
	                 circle(radius);
	                 cmdlist.add(command);
	             }
	        }
	        
	        else if (cmd.equals("star")) {
	        	if (ind.length == 2) {
	                int size = Integer.parseInt(ind[1]);
	                penDown();
	                star(size);
	                cmdlist.add(command);
	            }
	        }
	    }
}

	 
