package com.cse3345.dateright;

import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.view.Menu;
import android.view.MenuItem;
import android.widget.TextView;

public class UserProfileActivity extends Activity {
	//Profile
	private JSONObject profileInfo;
	private TextView userNameText;
	private TextView emailText;
	private TextView firstNameText;
	private TextView lastNameText;
	private TextView sexText;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_user_profile);
		
		/**
		 * Catch intent **/
		Intent intent = getIntent();
		//message is login string
		String message = intent.getStringExtra(UserSearchActivity.EXTRA_MESSAGE);
		//System.out.println(message);
		try {
			profileInfo = new JSONObject(message);
		} catch (JSONException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
			System.out.println("JSON Error Yo");
		}
		
		userNameText = (TextView) findViewById(R.id.userNameText);
		emailText = (TextView) findViewById(R.id.emailText);
		firstNameText = (TextView) findViewById(R.id.firstNameText);
		lastNameText = (TextView) findViewById(R.id.lastNameText);
		sexText = (TextView) findViewById(R.id.sexText);
		
		//set values in JSONObject to text in text views
		try {
			userNameText.setText(profileInfo.get("UserName").toString());
			emailText.setText(profileInfo.get("Email").toString());
			firstNameText.setText(profileInfo.get("FirstName").toString());
			lastNameText.setText(profileInfo.get("LastName").toString());
			sexText.setText(profileInfo.get("Sex").toString());
		} catch (JSONException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		// Inflate the menu; this adds items to the action bar if it is present.
		getMenuInflater().inflate(R.menu.user_profile, menu);
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
}
