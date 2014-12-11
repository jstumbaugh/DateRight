package com.cse3345.dateright;

import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Context;
import android.os.AsyncTask;
import android.os.Bundle;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.Toast;

public class Register extends Activity {
	private Context context;
	private ProgressDialog pd;
	
	private EditText emailInput;
	private EditText usernameInput;
	private EditText passwordInput;
	private EditText fNameInput;
	private EditText lNameInput;
	private RadioGroup sexRadioGroup;
	private RadioButton sexRadioButton;
	private EditText securityInput;
	private Button createAccountButton;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_register);
		
		context = Register.this;
		
		/**
		 * Create Account 
		 * */
		emailInput = (EditText) findViewById(R.id.createAccountInputEmail);
		usernameInput = (EditText) findViewById(R.id.createAccountInputUserName);
		passwordInput = (EditText) findViewById(R.id.createAcountInputPass);
		fNameInput = (EditText) findViewById(R.id.createAccountInputFirstName);
		lNameInput = (EditText) findViewById(R.id.createAccountInputLastName);
		sexRadioGroup = (RadioGroup) findViewById(R.id.sexRadio);
		securityInput = (EditText) findViewById(R.id.createAccountInputSecurityQuestion);
		createAccountButton = (Button) findViewById(R.id.createAccountButton);
		//click listener
		createAccountButton.setOnClickListener(new OnClickListener(){
			public void onClick(View view){
				//get selected radio button from radioGroup
				int selectedId = sexRadioGroup.getCheckedRadioButtonId();
				sexRadioButton = (RadioButton) findViewById(selectedId);
				
				if(emailInput.getText().length() == 0 ||
						usernameInput.getText().length() == 0 ||
						passwordInput.getText().length() == 0 ||
						fNameInput.getText().length() == 0 ||
						lNameInput.getText().length() == 0 ||
						securityInput.getText().length() == 0) {
					welcomeMessageUI("Feild blank. Fill out all inputs to continue.");
				} else {
					//start create account process
					createAccountAsync task = new createAccountAsync();
					task.execute((Object[]) null);
				}
			}
		});
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.register, menu);
		return true;
	}

	@Override
	public boolean onOptionsItemSelected(MenuItem item) {
		// Handle action bar item clicks here. The action bar will
		// automatically handle clicks on the Home/Up button, so long
		// as you specify a parent activity in AndroidManifest.xml.
		int id = item.getItemId();
		if (id == R.id.action_settings) {
			return true;
		}
		return super.onOptionsItemSelected(item);
	}
	
	/*
	 * Display welcome message with first name on the UI thread
	 */
	public void welcomeMessageUI(final String message) {
		runOnUiThread(new Runnable() {

			@Override
			public void run() {
				Toast.makeText(context, message, Toast.LENGTH_LONG).show();
			}
		});
	}
	
	private class createAccountAsync extends AsyncTask<Object, Object, Object> {
		private boolean success = false;
		private Boolean emailExists = false;
		private Boolean jsonError = false;
		
		private JSONObject json;
		
		private String email, username, pass, fName, lName, sex, securityAnswer;
		private String userId;

		@Override
		protected void onPreExecute() {
			pd = new ProgressDialog(context);
			pd.setTitle("Creating Account....");
			pd.setMessage("Please wait.");
			pd.setCancelable(false);
			pd.setIndeterminate(true);
			pd.show();
		}

		@Override
		protected Object doInBackground(Object... params) {
			UserActions userFunction = new UserActions();
			//grab data
			email = emailInput.getText().toString();
			username = usernameInput.getText().toString();
			pass = passwordInput.getText().toString();
			fName = fNameInput.getText().toString();
			lName = lNameInput.getText().toString();
			sex = sexRadioButton.getText().toString();
			securityAnswer = securityInput.getText().toString();
			
			json = userFunction.register(email, username, pass, fName, lName, sex, securityAnswer);

			// check for response
			try {
				System.out.println(json);
				if (json.get("Email").toString()
						.length() != 0) {
					System.out.println("HELLO THERE: " + json.get("Email"));
					userId = json.get("UserID")
							.toString();
					success = true;
				} else {
					// Error in login, signal post execute there was a problem
					success = false;
				}
			} catch (JSONException e) {
				e.printStackTrace();
			}
			return null;
		}

		@Override
		protected void onPostExecute(Object result) {
			if (pd != null) {
				pd.dismiss();
			}
			if (success) {
				// Create login session in shared preferences
				MainActivity.session.createLoginSession(fName, lName, email,
						userId, pass);
				welcomeMessageUI("Welcome,  " + fName);
				finish();
			} else {
				try {
					if(json.getBoolean("emailAlready")){
						emailExists = true;
					} else if(json.getBoolean("jsonError")){
						jsonError = true;
					}
				} catch (JSONException e) {
					e.printStackTrace();
				}
				//output error message
				if(emailExists){
					welcomeMessageUI("That email already exists. Try Again.");
				} else if(jsonError) {
					welcomeMessageUI("Error parsing input.");
				} else {
					welcomeMessageUI("Error");
				}
			}
		}
	}
}
