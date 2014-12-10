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
import android.widget.Toast;

public class Register extends Activity {
	private Context context;
	private ProgressDialog pd;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_register);
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
	
	private class loginAsync extends AsyncTask<Object, Object, Object> {
		private boolean success = false;

		String fName, lName, userId, email;

		@Override
		protected void onPreExecute() {
			pd = new ProgressDialog(context);
			pd.setTitle("Logging in....");
			pd.setMessage("Please wait.");
			pd.setCancelable(false);
			pd.setIndeterminate(true);
			pd.show();
		}

		@Override
		protected Object doInBackground(Object... params) {
			UserActions userFunction = new UserActions();
			//JSONObject json = userFunction.createUser(email, username, password, fName, lName, sex, );
			JSONObject json = null;

			// check for login response
			try {

				if (json.get("Email").toString()
						.length() != 0) {
					System.out.println("HELLO THERE: " + json.get("Email"));
					fName = json.get("FirstName")
							.toString();
					lName = json.get("LastName")
							.toString();
					email = json.get("Email")
							.toString();
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
			/*if (success) {
				// Create login session in shared preferences
				MainActivity.session.createLoginSession(fName, lName, email,
						userId, password);
				successMessageUI("Welcome,  " + fName);
				finish();
			} else {
				//Processing dialog failed
			}*/
		}
	}
}
