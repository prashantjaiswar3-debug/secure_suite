import type { Metadata } from "next";
import "./globals.css";

export const metadata: Metadata = {
  title: "Secure Suite",
  description: "A secure foundation for your next project.",
};

export default function RootLayout({ children }: Readonly<{ children: React.ReactNode }>) {
  return (
    <html lang="en">
      <body>{children}</body>
    </html>
  );
}