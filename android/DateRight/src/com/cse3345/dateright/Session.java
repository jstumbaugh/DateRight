package com.cse3345.dateright;

import java.util.HashMap;

import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.SharedPreferences.Editor;

public class Session {
	// Shared Preferences
	SharedPreferences pref;
	// Editor for Shared preferences
	Editor editor;
	// Context
	Context mContext;
	// Shared pref mode
	int PRIVATE_MODE = 0;
	// Sharedpref file name
	private static final String PREF_NAME = "ponypark";
	// Flag that keeps track of user session as logged in or not
	private static final String IS_LOGIN = "isLoggedIn";

	// Constructor
	public Session(Context context) {
		this.mContext = context;
		pref = mContext.getSharedPreferences(PREF_NAME, PRIVATE_MODE);
		editor = pref.edit();
	}

	/*
	 * Stores locally in SharedPreferences the user information to keep track of
	 * session
	 * 
	 * @ First name,Last Name, Email, User ID
	 */
	public void createLoginSession(String fName, String lName, String email,
			String userId, String username) {
		// Storing login value as true
		editor.putBoolean(IS_LOGIN, true);
		editor.putString(UserActions.KEY_fName, fName);
		editor.putString(UserActions.KEY_lName, lName);
		editor.putString(UserActions.KEY_email, email);
		editor.putString(UserActions.KEY_userID, userId);
		editor.putString(UserActions.KEY_username, username);
		// Submit changes
		editor.commit();
	}

	/*
	 * Using the keys returns mappings of user information
	 */
	public HashMap<String, String> getUserDetails() {
		HashMap<String, String> userInfo = new HashMap<String, String>();
		userInfo.put(UserActions.KEY_fName,
				pref.getString(UserActions.KEY_fName, null));
		userInfo.put(UserActions.KEY_lName,
				pref.getString(UserActions.KEY_lName, null));
		userInfo.put(UserActions.KEY_email,
				pref.getString(UserActions.KEY_email, null));
		userInfo.put(UserActions.KEY_userID,
				pref.getString(UserActions.KEY_userID, null));
		userInfo.put(UserActions.KEY_username,
				pref.getString(UserActions.KEY_username, null));
		return userInfo;
	}

	/*
	 * Resets the shared preferences
	 */
	public void logoutUser() {
		editor.clear();
		editor.commit();
	}

	/*
	 * Returns true if the user is logged in and false if not based upon
	 * IS_LOGIN flag key.
	 */
	public boolean isLoggedIn() {
		return pref.getBoolean(IS_LOGIN, false);
	}
}
