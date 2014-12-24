/**
* Cloud Computing Final Project - Event Recommendation System
* Extracts events using the SeatGeek API for a given city.
* Karan Kaul - kak2210@columbia.edu, Umang Patel - ujp2001@columbia.edu
*/
import java.io.*;
import java.net.*;
import java.util.HashMap;

import org.json.simple.JSONArray;
import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;
import org.json.simple.parser.ParseException;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.SQLException;

public class SeatGeekEvents {
	public static final HashMap<String, String> STATE_MAP;
	static {
		STATE_MAP = new HashMap<String, String>();
		STATE_MAP.put("AL", "Alabama");
		STATE_MAP.put("AK", "Alaska");
		STATE_MAP.put("AB", "Alberta");
		STATE_MAP.put("AZ", "Arizona");
		STATE_MAP.put("AR", "Arkansas");
		STATE_MAP.put("BC", "British Columbia");
		STATE_MAP.put("CA", "California");
		STATE_MAP.put("CO", "Colorado");
		STATE_MAP.put("CT", "Connecticut");
		STATE_MAP.put("DE", "Delaware");
		STATE_MAP.put("DC", "District Of Columbia");
		STATE_MAP.put("FL", "Florida");
		STATE_MAP.put("GA", "Georgia");
		STATE_MAP.put("GU", "Guam");
		STATE_MAP.put("HI", "Hawaii");
		STATE_MAP.put("ID", "Idaho");
		STATE_MAP.put("IL", "Illinois");
		STATE_MAP.put("IN", "Indiana");
		STATE_MAP.put("IA", "Iowa");
		STATE_MAP.put("KS", "Kansas");
		STATE_MAP.put("KY", "Kentucky");
		STATE_MAP.put("LA", "Louisiana");
		STATE_MAP.put("ME", "Maine");
		STATE_MAP.put("MB", "Manitoba");
		STATE_MAP.put("MD", "Maryland");
		STATE_MAP.put("MA", "Massachusetts");
		STATE_MAP.put("MI", "Michigan");
		STATE_MAP.put("MN", "Minnesota");
		STATE_MAP.put("MS", "Mississippi");
		STATE_MAP.put("MO", "Missouri");
		STATE_MAP.put("MT", "Montana");
		STATE_MAP.put("NE", "Nebraska");
		STATE_MAP.put("NV", "Nevada");
		STATE_MAP.put("NB", "New Brunswick");
		STATE_MAP.put("NH", "New Hampshire");
		STATE_MAP.put("NJ", "New Jersey");
		STATE_MAP.put("NM", "New Mexico");
		STATE_MAP.put("NY", "New York");
		STATE_MAP.put("NF", "Newfoundland");
		STATE_MAP.put("NC", "North Carolina");
		STATE_MAP.put("ND", "North Dakota");
		STATE_MAP.put("NT", "Northwest Territories");
		STATE_MAP.put("NS", "Nova Scotia");
		STATE_MAP.put("NU", "Nunavut");
		STATE_MAP.put("OH", "Ohio");
		STATE_MAP.put("OK", "Oklahoma");
		STATE_MAP.put("ON", "Ontario");
		STATE_MAP.put("OR", "Oregon");
		STATE_MAP.put("PA", "Pennsylvania");
		STATE_MAP.put("PE", "Prince Edward Island");
		STATE_MAP.put("PR", "Puerto Rico");
		STATE_MAP.put("QC", "Quebec");
		STATE_MAP.put("RI", "Rhode Island");
		STATE_MAP.put("SK", "Saskatchewan");
		STATE_MAP.put("SC", "South Carolina");
		STATE_MAP.put("SD", "South Dakota");
		STATE_MAP.put("TN", "Tennessee");
		STATE_MAP.put("TX", "Texas");
		STATE_MAP.put("UT", "Utah");
		STATE_MAP.put("VT", "Vermont");
		STATE_MAP.put("VI", "Virgin Islands");
		STATE_MAP.put("VA", "Virginia");
		STATE_MAP.put("WA", "Washington");
		STATE_MAP.put("WV", "West Virginia");
		STATE_MAP.put("WI", "Wisconsin");
		STATE_MAP.put("WY", "Wyoming");
		STATE_MAP.put("YT", "Yukon Territory");
	}

	public static final HashMap<String, String> CATEGORY_MAP;
	static {
		CATEGORY_MAP = new HashMap<String, String>();
		CATEGORY_MAP.put("sports", "sports");
		CATEGORY_MAP.put("mlb", "sports");
		CATEGORY_MAP.put("nba", "sports");
		CATEGORY_MAP.put("baseball", "sports");
		CATEGORY_MAP.put("ncaa_baseball", "sports");
		CATEGORY_MAP.put("minor_league_baseball", "sports");
		CATEGORY_MAP.put("football", "sports");
		CATEGORY_MAP.put("nfl", "sports");
		CATEGORY_MAP.put("ncaa_football", "sports");
		CATEGORY_MAP.put("basketball", "sports");
		CATEGORY_MAP.put("ncaa_basketball", "sports");
		CATEGORY_MAP.put("ncaa_womens_basketball", "sports");
		CATEGORY_MAP.put("nba_dleague", "sports");
		CATEGORY_MAP.put("wnba", "sports");
		CATEGORY_MAP.put("ncaa_hockey", "sports");
		CATEGORY_MAP.put("hockey", "sports");
		CATEGORY_MAP.put("nhl", "sports");
		CATEGORY_MAP.put("minor_league_hockey", "sports");
		CATEGORY_MAP.put("soccer", "sports");
		CATEGORY_MAP.put("mls", "sports");
		CATEGORY_MAP.put("ncaa_soccer", "sports");
		CATEGORY_MAP.put("european_soccer", "sports");
		CATEGORY_MAP.put("international_soccer", "sports");
		CATEGORY_MAP.put("world_cup", "sports");
		CATEGORY_MAP.put("auto_racing", "sports");
		CATEGORY_MAP.put("nascar", "sports");
		CATEGORY_MAP.put("nascar_sprintcup", "sports");
		CATEGORY_MAP.put("nascar_nationwide", "sports");
		CATEGORY_MAP.put("indycar", "sports");
		CATEGORY_MAP.put("f1", "sports");
		CATEGORY_MAP.put("monster_truck", "sports");
		CATEGORY_MAP.put("motocross", "sports");
		CATEGORY_MAP.put("golf", "sports");
		CATEGORY_MAP.put("pga", "sports");
		CATEGORY_MAP.put("lpga", "sports");
		CATEGORY_MAP.put("fighting", "sports");
		CATEGORY_MAP.put("boxing", "sports");
		CATEGORY_MAP.put("wrestling", "sports");
		CATEGORY_MAP.put("mma", "sports");
		CATEGORY_MAP.put("wwe", "sports");
		CATEGORY_MAP.put("animal_sports", "animals");
		CATEGORY_MAP.put("tennis", "sports");
		CATEGORY_MAP.put("rodeo", "animals");
		CATEGORY_MAP.put("extreme_sports", "sports");
		CATEGORY_MAP.put("olympic_sports", "sports");
		CATEGORY_MAP.put("horse_racing", "animals");
		CATEGORY_MAP.put("music_festival", "music");
		CATEGORY_MAP.put("concert", "music");
		CATEGORY_MAP.put("theater", "movies_film");
		CATEGORY_MAP.put("classical", "movies_film");
		CATEGORY_MAP.put("classical_opera", "movies_film");
		CATEGORY_MAP.put("classical_vocal", "music");
		CATEGORY_MAP.put("classical_orchestral_instrumental", "music");
		CATEGORY_MAP.put("cirque_du_soleil", "performing_arts");
		CATEGORY_MAP.put("broadway_tickets_national", "movies_film");
		CATEGORY_MAP.put("comedy", "comedy");
		CATEGORY_MAP.put("family", "family_fun_kids");
		CATEGORY_MAP.put("dance_performance_tour", "performing_arts");
		CATEGORY_MAP.put("film", "movies_film");
		CATEGORY_MAP.put("literary", "books");
	}

	public static final HashMap<String, String> CATEGORY_NAME;
	static {
		CATEGORY_NAME = new HashMap<String, String>();
		CATEGORY_NAME.put("music", "Concerts&amp;TourDates");
		CATEGORY_NAME.put("comedy", "Comedy");
		CATEGORY_NAME.put("family_fun_kids", "Kids&amp;Family");
		CATEGORY_NAME.put("movies_film", "Film");
		CATEGORY_NAME.put("books", "Literary&amp;Books");
		CATEGORY_NAME.put("performing_arts", "PerformingArts");
		CATEGORY_NAME.put("animals", "Pets");
		CATEGORY_NAME.put("sports", "Sports");
	}

	public static void main(String[] args) throws IOException {
		String city = args[0];
		try {
			Class.forName("com.mysql.jdbc.Driver");
		} catch (ClassNotFoundException e) {
			e.printStackTrace();
		}

		String jdbcUrl = DATABASE_URL;
		Connection con = null;
		try {
			con = DriverManager.getConnection(jdbcUrl, DATABASE_USERNAME,
					DATABASE_PASSWORD);

		} catch (SQLException e1) {
			e1.printStackTrace();
		}
		int page = 1;
		int maxPage = 100;
		while (page <= maxPage) {
			int perPage = 25;
			String url_test = "http://api.seatgeek.com/2/events?datetime_utc.gt=2014-12-24&venue.city="
					+ city
					+ "&per_page="
					+ perPage 
					+ "&page="
					+ page;
			
			URL url = new URL(url_test);
			HttpURLConnection connection = (HttpURLConnection) url
					.openConnection();
			connection.setRequestMethod("GET");
			boolean redirect = false;
			int responseCode = connection.getResponseCode();

			if (responseCode != HttpURLConnection.HTTP_OK) {
				if (responseCode == HttpURLConnection.HTTP_MOVED_TEMP
						|| responseCode == HttpURLConnection.HTTP_MOVED_PERM
						|| responseCode == HttpURLConnection.HTTP_SEE_OTHER)
					redirect = true;
			}

			System.out.println("Response Code: " + responseCode);

			if (redirect) {
				String newUrl = connection.getHeaderField("Location");
				connection = (HttpURLConnection) new URL(newUrl)
						.openConnection();
				System.out.println("Redirect to URL: " + newUrl);
			}

			BufferedReader in = new BufferedReader(new InputStreamReader(
					connection.getInputStream()));
			String inputLine;
			StringBuffer response = new StringBuffer();

			while ((inputLine = in.readLine()) != null) {
				response.append(inputLine);
			}
			in.close();

			try {
				JSONParser jsonParser = new JSONParser();
				JSONObject jsonObject = (JSONObject) jsonParser
						.parse(new String(response));
				Long l = new Long(
						(long) ((JSONObject) jsonObject.get("meta"))
								.get("total"));
				maxPage = (int) Math.ceil(l.doubleValue() / 25);
				JSONArray jsonArray = (JSONArray) jsonObject.get("events");
				for (int j = 0; j < jsonArray.size(); j++) {
					JSONObject venue = (JSONObject) jsonArray.get(j);
					String description = "", eventId = "", eventName = "", eventUrl = "", city = "", address = "", postcode = "", state = "", longitude = "", latitude = "", venueName = "", venueUrl = "", venueId = "", date_tbd = "true";
					if (venue.get("short_title").equals(null)) {
						description = "\n";
					} else {
						description = (venue.get("short_title")).toString()
								+ "\n";
					}
					if (!venue.get("id").equals(null))
						eventId = (venue.get("id")).toString();
					if (!venue.get("title").equals(null))
						eventName = (venue.get("title")).toString();
					if (!venue.get("url").equals(null))
						eventUrl = (venue.get("url")).toString();
					if (!((JSONObject) venue.get("venue")).get("city").equals(
							null))
						city = ((JSONObject) venue.get("venue")).get("city")
								.toString();
					if (!((JSONObject) venue.get("venue")).get("address")
							.equals(null))
						address = ((JSONObject) venue.get("venue")).get(
								"address").toString();
					if (!((JSONObject) venue.get("venue")).get("postal_code")
							.equals(null))
						postcode = ((JSONObject) venue.get("venue")).get(
								"postal_code").toString();
					if (!((JSONObject) venue.get("venue")).get("state").equals(
							null))
						state = STATE_MAP.get(((JSONObject) venue.get("venue"))
								.get("state").toString());
					String country = "United States of America";
					if (!((JSONObject) ((JSONObject) venue.get("venue"))
							.get("location")).get("lon").equals(null))
						longitude = ((JSONObject) ((JSONObject) venue
								.get("venue")).get("location")).get("lon")
								.toString();
					if (!((JSONObject) ((JSONObject) venue.get("venue"))
							.get("location")).get("lat").equals(null))
						latitude = ((JSONObject) ((JSONObject) venue
								.get("venue")).get("location")).get("lat")
								.toString();
					if (!((JSONObject) venue.get("venue")).get("name").equals(
							null))
						;
					venueName = ((JSONObject) venue.get("venue")).get("name")
							.toString();
					if (!((JSONObject) venue.get("venue")).get("url").equals(
							null))
						;
					venueUrl = ((JSONObject) venue.get("venue")).get("url")
							.toString();
					if (!((JSONObject) venue.get("venue")).get("id").equals(
							null))
						;
					venueId = ((JSONObject) venue.get("venue")).get("id")
							.toString();
					if (!venue.get("date_tbd").equals(null))
						date_tbd = (venue.get("date_tbd")).toString();
					String datetime_loc = "";
					String startTime = "";
					if (date_tbd == "false") {
						datetime_loc = (String) (venue.get("datetime_local")
								.toString());
						startTime = datetime_loc.replace('T', ' ');
					}

					JSONArray performers = (JSONArray) venue.get("performers");
					for (int i = 0; i < performers.size(); i++) {
						String performer = ((JSONObject) performers.get(i))
								.get("name").toString();
						String performerType = ((JSONObject) performers.get(i))
								.get("type").toString();
						description = description + performer.replace('_', ' ')
								+ " " + performerType.replace('_', ' ') + "\n";
					}
					JSONArray categories = (JSONArray) venue.get("taxonomies");
					for (int i = 0; i < categories.size(); i++) {
						String category = ((JSONObject) categories.get(i)).get(
								"name").toString();
						description = description + category.replace('_', ' ')
								+ "\n";
					}
					PreparedStatement pst;
					try {
						String sql = "insert ignore into SeatGeekEvents(eventId, eventName, eventUrl, eventDescription, city, state, country, address, postcode, latitude, longitude, venueName, venueUrl, venueId, startTime) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
						pst = con.prepareStatement(sql);
						pst.setString(1, eventId);
						pst.setString(2, eventName);
						pst.setString(3, eventUrl);
						pst.setString(4, description);
						pst.setString(5, city);
						pst.setString(6, state);
						pst.setString(7, country);
						pst.setString(8, address);
						pst.setString(9, postcode);
						pst.setString(10, latitude);
						pst.setString(11, longitude);
						pst.setString(12, venueName);
						pst.setString(13, venueUrl);
						pst.setString(14, venueId);
						pst.setString(15, startTime);
						pst.executeUpdate();
					} catch (SQLException e) {
						e.printStackTrace();
					}

					for (int i = 0; i < categories.size(); i++) {
						String seatGeekCategory = ((JSONObject) categories
								.get(i)).get("name").toString();
						String categoryId = CATEGORY_MAP.get(seatGeekCategory);
						String categoryName = CATEGORY_NAME.get(categoryId);
						PreparedStatement pstCategory;
						try {
							String sql = "insert ignore into SeatGeekCategories(eventId, categoryId, categoryName, seatGeekCategoryName) values (?, ?, ?, ?)";
							pstCategory = con.prepareStatement(sql);
							pstCategory.setString(1, eventId);
							pstCategory.setString(2, categoryId);
							pstCategory.setString(3, categoryName);
							pstCategory.setString(4, seatGeekCategory);
							pstCategory.executeUpdate();
						} catch (SQLException e) {
							e.printStackTrace();
						}
					}
				}
			} catch (ParseException e) {
				e.printStackTrace();
			}

			page++;
		}
	}
}
