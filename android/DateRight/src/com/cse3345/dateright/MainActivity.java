package com.cse3345.dateright;

import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.io.Reader;
import java.io.UnsupportedEncodingException;
import java.net.HttpURLConnection;
import java.net.URL;

import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.RadioButton;
import android.widget.RadioGroup;
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

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_main);
		
		// Session class instance
		session = new Session(getApplicationContext());
		
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
		 * RANDOM BUTTON SECTION
		 * */
		randomButton = (Button) findViewById(R.id.randomButton);
		
		randomButton.setOnClickListener(new OnClickListener() {
			public void onClick(View view) {
				Intent intent = new Intent(MainActivity.this, DisplayRandomActivity.class);
				startActivity(intent);
			}
		});
		
		/**
		 * LOGIN BUTTON SECTION
		 * */
		loginButton = (Button) findViewById(R.id.loginButton);
		//Set on click listener for login/logout button 
		if(session.isLoggedIn()){
			changeLoginLogout(true);
		} else {
			changeLoginLogout(false);
		}
		
		/*
		loginButton = (Button) findViewById(R.id.loginButton);
		
		loginButton.setOnClickListener(new OnClickListener() {
			public void onClick(View view) {
				Intent intent = new Intent(MainActivity.this, Login.class);
				startActivity(intent);
			}
		}); */
		
		//SESSION OUTPUT 
		System.out.println("SESSION CONTROL CHECK: " + session.isLoggedIn());
		if(session.isLoggedIn()){
			System.out.println(session.getUserDetails().get(UserActions.KEY_fName));
			System.out.println(session.getUserDetails().get(UserActions.KEY_lName));
		}
		/////////////////////////////
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
				Toast.makeText(MainActivity.this, message, Toast.LENGTH_LONG).show();
			}
		});
	}
}
