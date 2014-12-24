/**
* Cloud Computing Final Project - Event Recommendation System
* Calculates the prior rating for the recommendation system of all users.
* Karan Kaul - kak2210@columbia.edu, Umang Patel - ujp2001@columbia.edu
*/
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.Collections;
import java.util.HashMap;

public class FrequencyCount {

	public static final HashMap<String, String> CATEGORY_MAP;
	static {
		CATEGORY_MAP = new HashMap<String, String>();

		CATEGORY_MAP.put("attractions", "Museums &amp; Attractions");
		CATEGORY_MAP.put("holiday", "Holiday");
		CATEGORY_MAP.put("family_fun_kids", "Kids &amp; Family");
		CATEGORY_MAP.put("learning_education", "Education");
		CATEGORY_MAP.put("sports", "Sports");
		CATEGORY_MAP.put("performing_arts", "Performing Arts");
		CATEGORY_MAP.put("singles_social", "Nightlife &amp; Singles");
		CATEGORY_MAP.put("business", "Business &amp; Networking");
		CATEGORY_MAP.put("clubs_associations", "Organizations &amp; Meetups");
		CATEGORY_MAP.put("community", "Neighborhood");
		CATEGORY_MAP.put("food", "Food &amp; Wine");
		CATEGORY_MAP.put("music", "Concerts &amp; Tour Dates");
		CATEGORY_MAP.put("art", "Art Galleries &amp; Exhibits");
		CATEGORY_MAP.put("outdoors_recreation", "Outdoors &amp; Recreation");
		CATEGORY_MAP.put("support", "Health &amp; Wellness");
		CATEGORY_MAP.put("schools_alumni", "University &amp; Alumni");
		CATEGORY_MAP.put("books", "Literary &amp; Books");
		CATEGORY_MAP.put("conference", "Conferences &amp; Tradeshows");
		CATEGORY_MAP.put("religion_spirituality", "Religion &amp; Spirituality");
		CATEGORY_MAP.put("fundraisers", "Fundraising &amp; Charity");
		CATEGORY_MAP.put("technology", "Technology");
		CATEGORY_MAP.put("sales", "Sales &amp; Retail");
		CATEGORY_MAP.put("politics_activism", "Politics &amp; Activism");
		CATEGORY_MAP.put("movies_film", "Film");
		CATEGORY_MAP.put("comedy", "Comedy");
		CATEGORY_MAP.put("science", "Science");
	}

	public static void main(String[] args) {
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

		ArrayList<String> users = new ArrayList<String>();
		try {
			String query = "SELECT fid from User";
			Statement st = con.createStatement();
			ResultSet rs = st.executeQuery(query);

			while (rs.next()) {
				users.add(rs.getString("fid"));
			}
			st.close();
		} catch (Exception e) {
			e.printStackTrace();
		}

		try {
			for (String fid : users) {
				System.out.println("User: " + fid);

				HashMap<String, Integer> frequency = new HashMap<String, Integer>();
				String tables[] = { "interests_map", "books_map", "games_map",
						"movies_map", "music_map", "pagelike_map" };
				for (int tableIndex = 0; tableIndex < tables.length; tableIndex++) {
					String query = "SELECT count(cid) as freq, cid, cname from "
							+ tables[tableIndex]
							+ " where fid=\""
							+ fid
							+ "\" group by cid, cname";
					Statement st = con.createStatement();
					ResultSet rs = st.executeQuery(query);

					while (rs.next()) {
						int freq = rs.getInt("freq");
						String id = rs.getString("cid");
						String name = rs.getString("cname");
						if (frequency.containsKey(id)) {
							freq = freq + frequency.remove(id);
						}
						if (!(id.equalsIgnoreCase("none") || id
								.equalsIgnoreCase("other")))
							frequency.put(id, freq);
					}
					st.close();
				}
				int max = Collections.max(frequency.values());
				int min = Collections.min(frequency.values());

				float binSize = ((float) max - (float) min) / 5;

				float bins[] = new float[5];
				for (int i = 0; i < 5; i++) {
					bins[i] = (float) min + ((i + 1) * binSize);
				}

				for (String cid : frequency.keySet()) {
					int bin;
					if (frequency.get(cid) < bins[0])
						bin = 1;
					else if (frequency.get(cid) < bins[1])
						bin = 2;
					else if (frequency.get(cid) < bins[2])
						bin = 3;
					else if (frequency.get(cid) < bins[3])
						bin = 4;
					else
						bin = 5;
					if (bin <= 1)
						bin = 0;
					System.out.println(cid + ", " + bin);
					String ratingSql = "replace into prior_rating(fid, cid, rating) values(?, ?, ?)";
					PreparedStatement pst = con.prepareStatement(ratingSql);
					pst.setString(1, fid);
					pst.setString(2, cid);
					pst.setInt(3, bin);
					pst.executeUpdate();

				}

				for (String everyCategory : CATEGORY_MAP.keySet()) {
					if (!frequency.containsKey(everyCategory)) {
						int bin = 0;
						String ratingSql = "replace into prior_rating(fid, cid, rating) values(?, ?, ?)";
						PreparedStatement pst = con.prepareStatement(ratingSql);
						pst.setString(1, fid);
						pst.setString(2, everyCategory);
						pst.setInt(3, bin);
						pst.executeUpdate();
					}
				}
				System.out.println();
			}

		} catch (Exception e) {
			e.printStackTrace();
		}
	}

}