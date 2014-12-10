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

public class MainActivity extends Activity {
	protected static final String EXTRA_MESSAGE = "com.cse3345.DateRight.MESSAGE";
	//Search
	private EditText searchInput;
	private Button searchButton;
	
	private Button randomButton;
	
	//Network variables
	private static final String DEBUG_TAG = "HttpExample - getRandomIdea";
	private TextView textView;
	
	//Session Class
	public static Session session;

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
}
