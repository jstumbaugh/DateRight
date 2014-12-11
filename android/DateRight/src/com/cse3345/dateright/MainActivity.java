package com.cse3345.dateright;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

public class MainActivity extends Activity {
	protected static final String EXTRA_MESSAGE = "com.cse3345.DateRight.MESSAGE";
	//Search
	private EditText searchInput;
	private Button searchButton;
	//random
	private Button randomButton;
	//Network variables
	private static final String DEBUG_TAG = "HttpExample - getRandomIdea";
	private TextView textView;
	//Session Class
	public static Session session;
	//Login
	private Button loginButton;
	//Register
	private Button registerButton;
	//instance
	private static MainActivity instance;
	public static MainActivity getInstance() {
		if(instance !=null){
			return instance;
		}
		else{
			instance=new MainActivity();
			return instance;
		}
	}
	//async 
	private Context context;
	private ProgressDialog pd;
	//random
	private TextView randomName;
	private TextView randomTime;
	private TextView randomDesc;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);
		
		// Session class instance
		session = new Session(getApplicationContext());
		System.out.println("Session: " + session.getUserDetails());
		System.out.println("Logged in? " + session.isLoggedIn());
		
		// Context
		context = MainActivity.this;
		
		/**
		 * SEARCH BUTTON SECTION
		 * */
		searchInput = (EditText) findViewById(R.id.searchInput);
		searchButton = (Button) findViewById(R.id.searchButton);
		searchButton.setOnClickListener(new OnClickListener(){
			public void onClick(View view) {
				Intent intent = new Intent(MainActivity.this, GuestSearchActivity.class);
				String message = searchInput.getText().toString();
				//System.out.println(message);
				intent.putExtra(EXTRA_MESSAGE, message);
				startActivity(intent);
			}
		});
		
		/**
		 * RANDOM DATE SECTION
		 * */
		randomName = (TextView) findViewById(R.id.randomName);
		randomTime = (TextView) findViewById(R.id.randomTime);
		randomDesc = (TextView) findViewById(R.id.randomDesc);
		randomAsync task = new randomAsync();
		task.execute((Object[]) null);
		randomButton = (Button) findViewById(R.id.randomButton);
		
		randomButton.setOnClickListener(new OnClickListener() {
			public void onClick(View view) {
				randomAsync task = new randomAsync();
				task.execute((Object[]) null);
			}
		});
		
		/**
		 * LOGIN BUTTON SECTION
		 * */
		loginButton = (Button) findViewById(R.id.loginButton);
		assignTextForLogin();
		
		/**
		 * REGISTER BUTTON SECTION
		 * */
		registerButton = (Button) findViewById(R.id.registerButton);
		registerButton.setOnClickListener(new OnClickListener(){
			public void onClick(View view) {
				Intent intent = new Intent(MainActivity.this, Register.class);
				startActivity(intent);
				System.out.println("Done with register");
			}
		});
		
		//SESSION OUTPUT 
		System.out.println("SESSION CONTROL CHECK: " + session.isLoggedIn());
		if(session.isLoggedIn()){
			System.out.println(session.getUserDetails().get(UserActions.KEY_fName));
			System.out.println(session.getUserDetails().get(UserActions.KEY_lName));
		}
		/////////////////////////////
	}
	
	public void assignTextForLogin(){
		//Set on click listener for login/logout button 
		if(session.isLoggedIn()){
			//change to logout
			changeLoginLogout(true);
		} else {
			//change to login
			changeLoginLogout(false);
		}
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.main, menu);
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
	
	public void changeLoginLogout(boolean loggedIn) {
		if(loggedIn == true) {
			//Change functionality of button to Log out
			loginButton.setText("Logout");
			loginButton.setOnClickListener(new OnClickListener() {
				public void onClick(View view) {
					session.logoutUser();
					logoutMessageUI("Successfully Logged Out!");
					//If pressed, log out button will switch to log in
					changeLoginLogout(false);
				}
			});
			
		} else {
			//Change functionality of button to Log in
			loginButton.setText("Login");
			loginButton.setOnClickListener(new OnClickListener() {
				public void onClick(View view) {
					Intent intent = new Intent(MainActivity.this, Login.class);
					startActivity(intent);
					//if pressed, log in button will switch to log out
					changeLoginLogout(true);
				}
			});
		}
	}
	
	/*
	 * Display logout message on the UI thread
	 */
	public void logoutMessageUI(final String message) {
		runOnUiThread(new Runnable() {

			@Override
			public void run() {
				Toast.makeText(context, message, Toast.LENGTH_LONG).show();
			}
		});
	}
	
	private class randomAsync extends AsyncTask<Object, Object, Object> {
		private boolean success = false;

		String name, timestamp, description;

		@Override
		protected void onPreExecute() {
			pd = new ProgressDialog(context);
			pd.setTitle("Getting your date idea....");
			pd.setMessage("Please wait.");
			pd.setCancelable(false);
			pd.setIndeterminate(true);
			pd.show();
		}

		@Override
		protected Object doInBackground(Object... params) {
			UserActions userFunction = new UserActions();
			JSONArray json = userFunction.randomDate();

			// check for Random Info
			try {
				if (json.getJSONObject(0).get("DatePlanID").toString()
						.length() != 0) {
					name = json.getJSONObject(0).get("Name")
							.toString();
					timestamp = json.getJSONObject(0).get("Timestamp")
							.toString();
					description = json.getJSONObject(0).get("Description")
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
				//MainActivity.session.createLoginSession(fName, lName, email,
				//		userId, password);
				updateRandomDate(name, timestamp, description);
			} else {
				// progress bar never stops
			}
		}
	}
	
	public void updateRandomDate(String name, String timestamp, String description){
		//Set TextViews to reflect new data
		randomName.setText(name);
		randomTime.setText(timestamp);
		randomDesc.setText(description);
	}
}
