package oops;

	import javax.swing.*;
	import java.awt.*;
	import java.util.HashMap;
	
	//	Requirement 6
	public class LoginPage extends JFrame {

		private static final long serialVersionUID = 1L;
		private JTextField username_Field;
		private JPasswordField password_Field;
		private JButton loginButton;
		private static final HashMap<String, String> validUsers = new HashMap<>();
		
		static {
			// Username and Password to open GUI.
			validUsers.put("user", "pass");
		}

		public LoginPage() {
			super("Login");
			setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
			setLayout(new BorderLayout());

			// Add form fields
			JPanel formPanel = new JPanel(new GridLayout(2, 2));
			JOptionPane.showMessageDialog(this, "Click on 'OK' to proceed to the login form.\n To open the LBU_TURTLE GUI, enter the username as 'user' and password as 'pass'");

			JLabel userLabel = new JLabel("Username:(user)");
			username_Field = new JTextField();
			formPanel.add(userLabel);
			formPanel.add(username_Field);

			JLabel passLabel = new JLabel("Password:(pass)");
			password_Field = new JPasswordField();
			formPanel.add(passLabel);
			formPanel.add(password_Field);
			add(formPanel, BorderLayout.CENTER);

			// Add login button.
			loginButton = new JButton("Login");
			add(loginButton, BorderLayout.SOUTH);

			// Set a size of JFrame.
			setSize(400, 100);
			setLocationRelativeTo(null);
			setVisible(true);

			// Add action listener to login button
			loginButton.addActionListener(e -> {
				String username = username_Field.getText();
				String password = new String(password_Field.getPassword());
				if (isValidUser(username, password)) {
					openMainPage();
					dispose();
				} 
				else {
                JOptionPane.showMessageDialog(this, "Invalid username or password");
				}
			});
		}

		// The isValidUser method takes two String arguments, username and password, and returns a boolean value(true or false).
		private static boolean isValidUser(String username, String password) {
			String expectedPassword = validUsers.get(username);
			return expectedPassword != null && expectedPassword.equals(password);
		}

		// Create and show the main page
		private static void openMainPage() {
			GraphicsSystem mainPage = new GraphicsSystem();
			mainPage.setVisible(true);
		}

		public static void main(String[] args) {
			LoginPage loginmain = new LoginPage();
		}
	}
