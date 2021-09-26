import 'package:flutter/material.dart';
import 'package:flutter_html/flutter_html.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:html/dom.dart' as dom;
import 'package:intl/intl.dart';
import '../models/post.dart';
import '../services/post_service.dart';

class HomeScreenPostsTab extends StatefulWidget {
  @override
  State createState() {
    return _HomeScreenPostsTabState();
  }
}

class _HomeScreenPostsTabState extends State {
  bool _forceReload = false;

  @override
  Widget build(BuildContext context) {
    return FutureBuilder<List<Post>>(
      future: PostsService.getInstance().posts(forceReload: _forceReload),
      builder: (context, snapshot) {
        if (snapshot.hasError) {
          print('HomeScreenPostsTab error: ${snapshot.error}');
          return const Center(
            child: Text('An error has occurred!'),
          );
        } else if (snapshot.hasData) {
          return RefreshIndicator(
            onRefresh: () async {
              setState(() => _forceReload = true);
            },
            child: PostsList(posts: snapshot.data!)
          );
        } else {
          return const Center(
            child: CircularProgressIndicator(),
          );
        }
      }
    );
  }
}

class PostsList extends StatelessWidget {
  const PostsList({Key? key, required this.posts}) : super(key: key);

  final List<Post> posts;

  @override
  Widget build(BuildContext context) {
    return ListView.builder(
      padding: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      itemCount: posts.length,
      itemBuilder: (context, index) {
        Post post = posts[index];
        return Container(
          margin: EdgeInsets.symmetric(vertical: 8),
          child: Card(
            child: Padding(
              padding: EdgeInsets.all(16),
              child: Column(
                children: [
                  Container(
                    width: double.infinity,
                    margin: EdgeInsets.only(bottom: 8),
                    child: Text(post.title, style: TextStyle(fontSize: 18, fontWeight: FontWeight.w500)),
                  ),

                  Container(
                    width: double.infinity,
                    child: Text('Written by ${post.user.name} on ${DateFormat('yyyy-MM-dd kk:mm').format(post.created_at)}', style: TextStyle(color: Colors.grey))
                  ),

                  Html(
                    data: post.body,
                    style: { "body": Style(margin: EdgeInsets.zero, padding: EdgeInsets.zero) },
                    onLinkTap: (String? url, RenderContext context, Map<String, String> attributes, dom.Element? element) async {
                      if (url != null) {
                        if (await canLaunch(url as String)) await launch(url as String);
                      }
                    }
                  )
                ]
              )
            )
          )
        );
      }
    );
  }
}
