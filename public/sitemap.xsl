<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" 
                xmlns:html="http://www.w3.org/TR/REC-html40"
                xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
                xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes"/>
  <xsl:template match="/">
    <html xmlns="http://www.w3.org/1999/xhtml">
      <head>
        <title>XML Sitemap - Makoons Play School</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <style type="text/css">
          body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #333;
            background-color: #fafafa;
            margin: 0;
            padding: 40px 20px;
          }
          .container {
            max-width: 1000px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            border: 1px solid #eaeaea;
          }
          h1 {
            color: #111;
            font-size: 28px;
            margin-top: 0;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
          }
          h1::after {
            content: "XML";
            font-size: 11px;
            background: #7f3f98;
            color: #fff;
            padding: 2px 6px;
            border-radius: 4px;
            margin-left: 10px;
            font-weight: bold;
            vertical-align: middle;
          }
          p {
            color: #666;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 25px;
          }
          a {
            color: #7f3f98;
            text-decoration: none;
            font-weight: 500;
          }
          a:hover {
            text-decoration: underline;
          }
          table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 14px;
          }
          th {
            background-color: #f7f7f8;
            color: #555;
            text-align: left;
            padding: 12px 15px;
            font-weight: 600;
            border-bottom: 2px solid #eaeaea;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
          }
          td {
            padding: 12px 15px;
            border-bottom: 1px solid #eaeaea;
            word-break: break-all;
          }
          tr:hover td {
            background-color: #fcfcfc;
          }
          .count {
            background-color: #f1ecf6;
            color: #7f3f98;
            padding: 4px 10px;
            border-radius: 99px;
            font-size: 13px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 20px;
          }
          .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #999;
            text-align: center;
            border-top: 1px solid #eaeaea;
            padding-top: 20px;
          }
        </style>
      </head>
      <body>
        <div class="container">
          <h1>Makoons Sitemap</h1>
          <p>This XML Sitemap is generated dynamically to help search engines like Google discover and index all posts, stories, printables, and video sessions on the Makoons platform.</p>

          <!-- Check if it is a Sitemap Index -->
          <xsl:if test="sitemap:sitemapindex">
            <div class="count">
              This Sitemap Index contains <xsl:value-of select="count(sitemap:sitemapindex/sitemap:sitemap)"/> sitemaps.
            </div>
            <table>
              <thead>
                <tr>
                  <th width="75%">Sitemap URL</th>
                  <th width="25%">Last Modified</th>
                </tr>
              </thead>
              <tbody>
                <xsl:for-each select="sitemap:sitemapindex/sitemap:sitemap">
                  <tr>
                    <td>
                      <xsl:variable name="sitemap_loc">
                        <xsl:value-of select="sitemap:loc"/>
                      </xsl:variable>
                      <a href="{$sitemap_loc}"><xsl:value-of select="sitemap:loc"/></a>
                    </td>
                    <td>
                      <xsl:value-of select="sitemap:lastmod"/>
                    </td>
                  </tr>
                </xsl:for-each>
              </tbody>
            </table>
          </xsl:if>

          <!-- Check if it is a Urlset sitemap -->
          <xsl:if test="sitemap:urlset">
            <div class="count">
              This Sitemap contains <xsl:value-of select="count(sitemap:urlset/sitemap:url)"/> URLs.
            </div>
            <table>
              <thead>
                <tr>
                  <th width="60%">URL</th>
                  <th width="15%">Change Freq.</th>
                  <th width="10%">Priority</th>
                  <th width="15%">Last Modified</th>
                </tr>
              </thead>
              <tbody>
                <xsl:for-each select="sitemap:urlset/sitemap:url">
                  <tr>
                    <td>
                      <xsl:variable name="url_loc">
                        <xsl:value-of select="sitemap:loc"/>
                      </xsl:variable>
                      <a href="{$url_loc}"><xsl:value-of select="sitemap:loc"/></a>
                    </td>
                    <td>
                      <xsl:value-of select="sitemap:changefreq"/>
                    </td>
                    <td>
                      <xsl:value-of select="sitemap:priority"/>
                    </td>
                    <td>
                      <xsl:value-of select="sitemap:lastmod"/>
                    </td>
                  </tr>
                </xsl:for-each>
              </tbody>
            </table>
          </xsl:if>

          <div class="footer">
            Generated by Makoons SEO Engine. Learn more about XML Sitemaps at <a href="https://sitemaps.org" target="_blank">sitemaps.org</a>.
          </div>
        </div>
      </body>
    </html>
  </xsl:template>
</xsl:stylesheet>
