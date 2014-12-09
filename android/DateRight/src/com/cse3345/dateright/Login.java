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
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

public class Login extends Activity {
	Button btnLogin;
	Button btnLinkToRegister;
	EditText inputEmail;
	EditText inputPassword;
	TextView loginErrorMsg;
	private Context context;
	private ProgressDialog pd;
	private String email, password;
	private static Login instance;
	private static final String TAG = "SignInTestActivity";
	private static final int REQUEST_CODE_RESOLVE_ERR = 9000;
	private static ProgressDialog mConnectionProgressDialog;
	private String fbId, fName, lName, userId;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_login);
		
		context = this;
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.login, menu);
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
	 * Display welcom message with first name on the UI thread
	 */
	public void welcomeMessageUI(final String message) {
		runOnUiThread(new Runnable() {

			@Override
			public void run() {
				Toast.makeText(context, message, Toast.LENGTH_LONG).show();
			}
		});
	}
	
	private class RegularLoginAsync extends AsyncTask<Object, Object, Object> {
		private boolean success = false;

		String fName, lName, userId;

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
			JSONObject json = userFunction.loginUser(email, password);

			// check for login response
			try {

				if (json.getJSONObject("UserInfo").get("Email").toString()
						.length() != 0) {
					fName = json.getJSONObject("UserInfo").get("FirstName")
							.toString();
					lName = json.getJSONObject("UserInfo").get("LastName")
							.toString();
					email = json.getJSONObject("UserInfo").get("Email")
							.toString();
					userId = json.getJSONObject("UserInfo").get("UserID")
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
						userId, password);
				welcomeMessageUI("Welcome,  " + fName);
				finish();
			} else {
				loginErrorMsg.setText("Incorrect email/password");
			}
		}
	}
}
